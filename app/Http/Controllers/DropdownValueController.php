<?php

namespace App\Http\Controllers;

use App\Models\DropdownType;
use App\Models\DropdownValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DropdownValueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $activeTab = 'Values';
        $types = DropdownType::with('values','parent')->get();
        $values = DropdownVALUE::with('type','parent')->get();
        return view('Heights.Admin.Dropdown.index', compact('types', 'values', 'activeTab'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $types = DropdownType::with(['values', 'parent'])->get();
        $values = DropdownVALUE::all();
        return view('Heights.Admin.Dropdown.create', compact('types', 'values'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate incoming request
        $validatedData = $request->validate([
            'value_name' => 'required|string',
            'description' => 'nullable|string',
            'dropdown_type_id' => 'required|integer',
            'parent_value_id' => 'nullable|integer',
            'status' => 'required|boolean',
        ]);

        // Check if the LovMaster entry already exists
        $exists = $this->checkIfValueExists(
            $request->value_name,
            $request->dropdown_type_id,
            $request->parent_value_id,
        );

        if ($exists) {
            return redirect()->back()->withErrors(['error' => 'Value already exists with this combination.'])->withInput();
        }

        DropdownValue::create($validatedData);

        return redirect()->route('values.index')->with('success', 'Value added successfully.');
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
        $value = DropdownVALUE::findOrFail($id);
        if ($value) {
            return response()->json($value);
        }
        return response()->json(['message' => 'Not found'], 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'value_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'dropdwon_type_id' => 'required|exists:dropdowntypes,id',
            'parent_value_id' => 'nullable|exists:dropdownvalues,id',
            'status' => 'required|in:0,1',
        ]);

        $exists = $this->checkIfValueExists(
            $request->value_name,
            $request->dropdown_type_id,
            $request->parent_value_id,
        );

        if ($exists) {
            return redirect()->back()->withErrors(['error' => 'Value already exists with this combination.'])->withInput();
        }

        $value = DropdownValue::findOrFail($id);
        $value->update($request->all());

        return redirect()->route('values.index')->with('success', 'Value updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return redirect()->back();
    }

    protected function checkIfValueExists($value_name, $dropdown_type_id, $parent_value_id = null, $id = null)
    {
        $query = DropdownValue::where('value_name', $value_name)
                            ->where('dropdown_type_id', $dropdown_type_id);

        if (!is_null($id)) {
            // Exclude the current record from the check
            $query->where('id', '!=', $id);
        }

        if (is_null($parent_value_id)) {
            return $query->exists();
        } else {
            return $query->where('parent_value_id', $parent_value_id)
                        ->exists();
        }
    }
}
