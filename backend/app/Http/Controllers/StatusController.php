<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Orders;
use App\Models\Shopify;

class StatusController extends Controller
{

    public function getStatus()
    {

        // Get all statuses

    $statuses = DB::table('statuses')
            ->select('s_id', 'name', 'color', 'description')
            ->orderBy('s_id')
            ->get();

        // Return the statuses as a JSON response
    return response()->json($statuses);

    }

    
 public function updateStatus(Request $request)
    {
        $request->validate([
            'direction' => 'required|in:next,previous',
            'currentStatus' => 'required|integer',
        ]);
      try{


      
        // Get current status sequence
        $currentStatus = DB::table('orders')
            ->select('status_id')
            ->where('shopify_order_id', $request->order_id)
            ->first();

        if ($currentStatus):
            $currentStatus = $currentStatus->status_id;
        else:
            $currentStatus = $request->currentStatus;
        endif;


        $nextStatus = ($request->direction == 'next' ? $currentStatus + 1 : $currentStatus - 1);    

            // check if next is allowed status
        $nextStatusExist = DB::table('statuses')
            ->where('s_id', $nextStatus)
            ->first();

            // Check if the requested status is valid
        if (!$nextStatusExist) {
            return response()->json(['error' => 'Invalid status transition'], 422);
        }
        // Start a transaction
        DB::beginTransaction();     


        Orders::updateOrCreate(
            ['shopify_order_id' => $request->order_id],
            ['status_id' => $nextStatus]
        );

        // Insert new status history record
        DB::table('order_status_history')->insert([
            'order_id' => $request->order_id,
            'status_id' => $nextStatus,
            //'user_id' => auth()->id(),
            'changed_at' => now(),
        ]);
    

        DB::commit();

        $shop = new Shopify($request->shop['id']); // Use the shop ID from the request

        // Update the order status in Shopify
        $shop->set_order_status($request->order_id, $nextStatusExist->name);

        
        return response()->json(['success' => true, 'message' => 'Status updated successfully'], 200);

    } catch (\Exception $e) {
        // // Log the error message
        // \Log::error('Error updating order status', [
        //     'order_id' => $request->order_id,
        //     'error' => $e->getMessage(),
        // ]);

        DB::rollBack();
        return response()->json(['error' => 'Failed to update status: ' . $e->getMessage()], 500);

    }
    }

}
