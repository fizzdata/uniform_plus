<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Shopify\Clients\Rest;


class OrderStageController extends Controller
{
    public function index()
    {
        // Get all stages ordered by position
        $stages = DB::table('order_stages')
            ->orderBy('position')
            ->get();
            
        return view('stages.index', compact('stages'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'color' => 'required|string|max:20',
            'position' => 'required|integer',
        ]);
        
        // Insert new stage
        DB::table('order_stages')->insert([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'color' => $validated['color'],
            'position' => $validated['position'],
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        return redirect()->route('stages.index');
    }
    
    public function update(Request $request, $stageId)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'color' => 'required|string|max:20',
            'position' => 'required|integer',
        ]);
        
        // Update existing stage
        DB::table('order_stages')
            ->where('id', $stageId)
            ->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'color' => $validated['color'],
                'position' => $validated['position'],
                'updated_at' => now(),
            ]);
            
        return redirect()->route('stages.index');
    }
    
    public function destroy($stageId)
    {
        // Delete the stage
        DB::table('order_stages')
            ->where('id', $stageId)
            ->delete();
            
        // Delete related mappings
        DB::table('order_stage_mappings')
            ->where('order_stage_id', $stageId)
            ->delete();
            
        return redirect()->route('stages.index');
    }
}
