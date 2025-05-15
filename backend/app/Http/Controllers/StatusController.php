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
            ->select('id', 'name', 'color','sequence')
            ->orderBy('sequence')
            ->get();

        // Return the statuses as a JSON response
    return response()->json($statuses);

    }

    
 public function updateStatus(Request $request)
    {
        // Get current status sequence
        $currentStatus = DB::table('order_status_history')
            ->where('order_id', $request->order_id)
            ->orderByDesc('changed_at')
            ->first();

            // Get next allowed status
        $nextStatus = DB::table('statuses')
            ->where('sequence', ($currentStatus->sequence ?? 0) + 1)
            ->first();

            // Check if the requested status is valid
        if (!$nextStatus) {
            return response()->json(['error' => 'Invalid status transition'], 422);
        }

        Orders::updateOrCreate(
            ['shopify_order_id' => $request->order_id],
            ['status_id' => $nextStatus->id]
        );

        // Insert new status history record
        DB::table('order_status_history')->insert([
            'order_id' => $request->order_id,
            'status_id' => $nextStatus->id,
            //'user_id' => auth()->id(),
            'changed_at' => now(),
        ]);

        return response()->json(['message' => 'Status updated successfully']);
    }

}
