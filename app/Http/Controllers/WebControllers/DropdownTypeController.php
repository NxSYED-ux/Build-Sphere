<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Models\DropdownType;
use App\Models\DropdownValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DropdownTypeController extends Controller
{
    public function index()
    {
        $activeTab = 'Types';
        $types = DropdownType::with('values','parent')->get();
        $values = DropdownValue::with('type','parent')->get();

        return view('Heights.Admin.Dropdown.index', compact('types', 'values', 'activeTab'));
    }

    public function create()
    {
        return redirect()->back();
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'type_name' => 'required|string|max:255|unique:dropdowntypes',
            'description' => 'nullable|string',
            'parent_type_id' => 'nullable|integer',
            'status' => 'required|boolean',
        ]);

        try {
            DropdownType::create($validatedData);
            return redirect()->route('types.index')->with('success', 'Type added successfully.');
        } catch (\Exception $e) {
            Log::error('Error in creating drop down type : ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while creating the Dropdown Type.');
        }
    }

    public function show(string $id)
    {
        return redirect()->back();
    }

    public function edit(string $id)
    {
        $type = DropdownType::findorfail($id);
        if ($type) {
            return response()->json($type);
        }
        return response()->json(['message' => 'Not found'], 404);
    }

    public function update(Request $request, string $id)
    {
        $type = DropdownType::findorfail($id);
        if (!$type) {
            return redirect()->back()->with('error', 'Dropdown Type not found.');
        }

        $validatedData = $request->validate([
            'type_name' => 'required|string|max:255|unique:dropdowntypes,type_name,' . $id . ',id',
            'description' => 'nullable|string',
            'parent_type_id' => 'nullable|integer',
            'status' => 'required|boolean',
        ]);

        try {
            $type->update($validatedData);
            return redirect()->route('types.index')->with('success', 'Type updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error in updating drop down type : ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while updating the Dropdown Type.');
        }
    }

    public function destroy(string $id)
    {
        return redirect()->back();
    }
}
