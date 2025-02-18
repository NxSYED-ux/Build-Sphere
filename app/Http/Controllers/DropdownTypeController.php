<?php

namespace App\Http\Controllers;

use App\Models\DropdownType;
use App\Models\DropdownValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DropdownTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $activeTab = 'Types';
        $types = DropdownType::with('values','parent')->get();
        $values = DropdownVALUE::with('type','parent')->get();
        return view('Heights.Dropdown.index', compact('types', 'values', 'activeTab'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return redirect()->back();
    }

    /**
     * Store a newly created resource in storage.
     */
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
            return redirect()->back()->with('error', 'An error occurred while creating the Dropdown Type.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return redirect()->back();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $type = DropdownType::findorfail($id);
        if ($type) {
            return response()->json($type);
        }
        return response()->json(['message' => 'Not found'], 404);
    }

    /**
     * Update the specified resource in storage.
     */
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
            return redirect()->back()->with('error', 'An error occurred while updating the Dropdown Type.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return redirect()->back();
    }
}
