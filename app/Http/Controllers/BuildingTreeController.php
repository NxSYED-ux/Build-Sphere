<?php

namespace App\Http\Controllers;

use App\Models\Building;

class BuildingTreeController extends Controller
{
    public function tree()
    {
        // $building->load([
        //     'address',
        //     'pictures',
        //     'organization.owner',
        //     'levels.units.pictures'
        // ]);

        $id = 1;

        $building = Building::with('address',
            'pictures',
            'organization.owner',
            'levels.units.pictures'
        )->findOrFail($id);

        // Extract necessary data
        $owner = $building->organization->owner;
        $levels = $building->levels;
        $units = $levels->flatMap->units;

        return view('Heights.Buildings.tree', compact('building', 'levels', 'units', 'owner'));
    }
}
