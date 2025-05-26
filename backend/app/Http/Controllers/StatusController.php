<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Orders;

class StatusController extends Controller
{

    public function getStatus()
    {

        // Get all statuses

    $statuses = DB::table('statuses')
            ->select('id', 'name', 'color', 'description')
            ->orderBy('id')
            ->get();

        // Return the statuses as a JSON response
    return response()->json($statuses);

    }

    
 public function updateStatus(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer',
            'next_status' => 'required|integer',
        ]);
      try{


      
        // Get current status sequence
        $currentStatus = DB::table('orders')
            ->join('statuses', 'orders.status_id', '=', 'statuses.id')
            ->select('statuses.sequence')
            ->where('shopify_order_id', $request->order_id)
            ->first();

            // Get next allowed status
        $nextStatus = DB::table('status_transitions')
            ->where('current_status_id', $currentStatus->status_id)
            ->where('next_status_id', $request->next_status)
            ->exists();

            // Check if the requested status is valid
        if (!$nextStatus) {
            return response()->json(['error' => 'Invalid status transition'], 422);
        }
        // Start a transaction
        DB::beginTransaction();     


        Orders::updateOrCreate(
            ['shopify_order_id' => $request->order_id],
            ['status_id' => $request->nextStatus]
        );

        // Insert new status history record
        DB::table('order_status_history')->insert([
            'order_id' => $request->order_id,
            'status_id' => $request->nextStatus,
            //'user_id' => auth()->id(),
            'changed_at' => now(),
        ]);

        DB::commit();
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
