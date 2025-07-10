<?php

namespace App\Services;



use App\Models\Building;
use App\Models\BuildingLevel;
use App\Models\BuildingUnit;
use App\Models\Organization;
use App\Models\Plan;
use App\Models\Role;

class AdminFiltersService
{
    private $allowedStatusesForBuilding = ['Approved', 'Under Review', 'For Re-Approval'];
    private $approvedStatusesForBuilding = ['Approved', 'For Re-Approval'];

    public function getAllowedStatusesForBuilding()
    {
        return $this->allowedStatusesForBuilding;
    }

    public function organizations()
    {
        return  Organization::orderBy('name', 'asc')
            ->select('id', 'name')
            ->get();
    }

    public function buildings()
    {
        return Building::whereIn('status', $this->allowedStatusesForBuilding)
            ->orderBy('name', 'asc')
            ->select('id', 'name', 'organization_id')
            ->get();
    }

    public function approvedBuildings()
    {
        return Building::whereIn('status',$this->approvedStatusesForBuilding )
            ->orderBy('name', 'asc')
            ->select('id', 'name', 'organization_id')
            ->get();
    }

    public function levels()
    {
        return BuildingLevel::whereHas('building', function ($query) {
            $query->whereIn('status', $this->allowedStatusesForBuilding);
        })
            ->orderBy('level_name', 'asc')
            ->select('id', 'level_name')
            ->get();
    }

    public function units()
    {
        return BuildingUnit::whereHas('building', function ($query) {
            $query->whereIn('status', $this->allowedStatusesForBuilding);
        })
            ->orderBy('unit_name', 'asc')
            ->select('id', 'unit_name')
            ->get();
    }

    public function checkBuildingAccess($id)
    {
        $building = Building::find($id);

        if (!$building || !in_array($building->status, $this->allowedStatusesForBuilding)) {
            return [
                'access' => false,
                'message' => 'Access denied to this building'
            ];
        }

        return [
            'access' => true,
            'building' => $building
        ];
    }

    public function roles(){
        return Role::select('id', 'name')
            ->orderBy('name', 'asc')
            ->get();
    }

    public function plans()
    {
        return Plan::select('id', 'name')
            ->orderBy('name', 'asc')
            ->get();
    }

}
