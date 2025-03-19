<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\ManagerBuilding;
use Illuminate\Http\Request;

class BuildingTreeController extends Controller
{
    public function tree(Request $request)
    {
        $user = $request->user() ?? abort(404, 'Page Not Found');

        $request->validate([
            'building_id' => 'nullable|integer|exists:buildings,id',
        ]);

        $building = null;
        $levels = null;
        $units = null;
        $owner = null;
        $buildingsDropDown = null;

        $token = $request->attributes->get('token');

        if (empty($token['organization_id']) || empty($token['role_name'])) {
            return view('Heights.Owner.Buildings.tree', compact('building', 'levels', 'units', 'owner', 'buildingsDropDown'));
        }

        $query = Building::where('organization_id', $token['organization_id']);

        if ($token['role_name'] === 'Manager') {
            $managerBuildings = ManagerBuilding::where('user_id', $user->id)->pluck('building_id')->toArray();
            if (!empty($managerBuildings)) {
                $query->whereIn('id', $managerBuildings);
            }
        }

        $buildingsDropDown = $query->pluck('name', 'id');

        $buildingId = $request->input('building_id') ?? $buildingsDropDown->keys()->first();

        if ($buildingId) {
            $building = Building::with([
                'address',
                'pictures',
                'organization.owner',
                'levels.units.pictures'
            ])->find($buildingId);

            if ($building) {
                $owner = optional($building->organization)->owner;
                $levels = $building->levels;
                $units = $levels->flatMap->units;
            }
        }

        return view('Heights.Owner.Buildings.tree', compact('building', 'levels', 'units', 'owner', 'buildingsDropDown'));
    }

}
