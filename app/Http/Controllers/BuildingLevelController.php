<?php

namespace App\Http\Controllers;

use App\Models\BuildingLevel;
use App\Models\Building;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

use Illuminate\Http\Request;

class BuildingLevelController extends Controller
{
    public function index(Request $request)
    {
        $buildingId = $request->input('building_id');
        $levelsQuery = BuildingLevel::with('building', 'creator', 'updater');
        if ($buildingId) {
            $levelsQuery->where('building_id', $buildingId);
        }
        $levels = $levelsQuery->get();
        return view('Heights.Admin.Levels.index', compact('levels'));
    }

    public function create()
    {
        $buildings = Building::all();
        return view('Heights.Admin.Levels.create', compact('buildings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'level_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'level_number' => 'required|integer',
            'status' => 'required|string|in:Approved,Rejected',
            'building_id' => 'required|exists:buildings,id',
        ]);

        BuildingLevel::create($validated);

        return redirect()->route('levels.index')->with('success', 'Building Level created successfully.');
    }

    public function show(BuildingLevel $buildingLevel)
    {
        return view('Heights.Admin.Levels.show', compact('buildingLevel'));
    }

    public function edit(BuildingLevel $level)
    {
        $level->load(['building']);
        $buildings = Building::all();
        return view('Heights.Admin.Levels.edit', compact('level', 'buildings'));
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'level_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'level_number' => 'required|integer',
            'status' => 'required|string|in:Approved,Rejected',
            'building_id' => 'required|exists:buildings,id',
        ]);

        $buildingLevel = BuildingLevel::findorfail($id);

        $buildingLevel->update([
            'level_name' => $request->level_name,
            'description' => $request->description,
            'level_number' => $request->level_number,
            'status' => $request->status,
            'building_id' => $request->building_id,
        ]);

        if(!$buildingLevel){
            return redirect()->back()->withInput()->with('error', 'An error occurred while creating the building.');
        }

        return redirect()->route('levels.index')->with('success', 'Building Level updated successfully.');
    }

    public function destroy(BuildingLevel $buildingLevel)
    {
        $buildingLevel->delete();

        return redirect()->route('levels.index')->with('success', 'Building Level deleted successfully.');
    }
}
