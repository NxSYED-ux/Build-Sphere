<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Models\Building;
use Illuminate\Http\Request;

class OwnerBuildingController extends Controller
{
    public function index(Request $request)
    {
        // Get the search query from the input
        $search = $request->input('search');

        // Query buildings and filter by search if a search term is provided
        $buildings = Building::with(['pictures', 'address', 'organization'])
            ->where('name', 'like', '%' . $search . '%')  // Search by name
            ->orWhere('remarks', 'like', '%' . $search . '%')  // Search by remarks
            ->orWhereHas('organization', function($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');  // Search by organization name
            })
            ->orWhereHas('address', function($query) use ($search) {
                $query->where('city', 'like', '%' . $search . '%');  // Search by city
            })
            ->paginate(10);  // Paginate the results

        // Return the view with the filtered buildings
        return view('Heights.Owner.Buildings.index', compact('buildings'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
