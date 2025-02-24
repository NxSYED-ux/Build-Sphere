<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Building;
use App\Models\BuildingDocument;
use App\Models\Organization;
use App\Models\DropdownType;
use App\Models\BuildingPicture;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

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
