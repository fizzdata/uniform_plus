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
            ->select('id','name', 'color', 'description')
            ->get();

        // Return the statuses as a JSON response
    return response()->json($statuses);

    }

    public function getStatusTransitions()
    {
        // Get all status transitions
        $transitions = DB::table('order_status_transitions')
            ->select('id', 'from_status', 'to_status')
            ->get();

        // Return the transitions as a JSON response
        return response()->json($transitions);
    }

    
 public function updateStatus(Request $request)
    {
        $request->validate([
            'currentStatus' => 'required|integer',
            'nextStatus' => 'required|integer',
            'direction' => 'required|in:previous,next',
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

        //check if allowed status transition

        if ($request->direction === 'next'):

        $nextStatus = DB::table('order_status_transitions') 
        ->where('from_status', $currentStatus)
        ->where('to_status', $request->nextStatus)
        ->value('to_status');
            // check if next is allowed status
        $nextStatusExist = DB::table('statuses')
            ->where('id', $nextStatus)
            ->first();
        elseif($request->direction === 'previous'):
                       $nextStatus = DB::table('order_status_transitions') 
        ->where('to_status', $currentStatus)
        ->where('from_status', $request->nextStatus)
        ->value('from_status');
                
        $nextStatusExist = DB::table('statuses')
            ->where('id', $nextStatus)
            ->first();
        endif;


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
        $shop->set_order_status($request->order_id, $nextStatusExist);

        
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
