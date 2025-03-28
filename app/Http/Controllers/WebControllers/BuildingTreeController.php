<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\ManagerBuilding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BuildingTreeController extends Controller
{
    public function tree(Request $request)
    {
        try {
            $user = $request->user() ?? abort(404, 'Page Not Found');

            $building = null;
            $levels = null;
            $units = null;
            $owner = null;
            $buildingsDropDown = null;
            $buildingId = $request->input('building_id');

            $token = $request->attributes->get('token');

            if (empty($token['organization_id']) || empty($token['role_name'])) {
                return $buildingId
                    ? redirect()->back()->with('error', 'Invalid Building ID')
                    : view('Heights.Owner.Buildings.tree', compact('building', 'levels', 'units', 'owner', 'buildingsDropDown'));
            }

            $query = Building::where('organization_id', $token['organization_id']);

            if ($token['role_name'] === 'Manager') {
                $managerBuildings = ManagerBuilding::where('user_id', $user->id)->pluck('building_id')->toArray();
                $query->whereIn('id', $managerBuildings);
            }

            $buildingsDropDown = $query->pluck('name', 'id');
            $buildingId = $buildingId ?? $buildingsDropDown->keys()->first();

            if (!$buildingId) {
                return view('Heights.Owner.Buildings.tree', compact('building', 'levels', 'units', 'owner', 'buildingsDropDown'));
            }

            if(!isset($buildingsDropDown[$buildingId])){
                return redirect()->back()->with('error', 'Invalid Building ID');
            }

            if ($buildingId) {
                $building = Building::with([
                    'address',
                    'pictures',
                    'organization.owner',
                    'levels',
                    'units.pictures'
                ])->find($buildingId);

                if ($building) {
                    $owner = optional($building->organization)->owner;
                    $levels = $building->levels ?? collect();
                    $units = $building->units ?? collect();
                }
            }

            return view('Heights.Owner.Buildings.tree', compact('building', 'levels', 'units', 'owner', 'buildingsDropDown'));
        } catch (\Exception $e) {
            Log::error('Error in Building Tree : ' . $e->getMessage());
            return back()->with('error', 'An error occurred, while loading the page.');
        }
    }

}
