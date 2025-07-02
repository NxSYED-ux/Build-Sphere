<?php

namespace App\Services;

use App\Models\Building;
use App\Models\BuildingLevel;
use App\Models\BuildingUnit;
use App\Models\Department;
use App\Models\ManagerBuilding;
use App\Models\Membership;
use App\Models\StaffMember;
use App\Models\User;

class OwnerFiltersService
{
    private $allowedStatusesForBuilding = ['Approved', 'Under Processing', 'Under Review', 'For Re-Approval', 'Rejected'];


    public function getAllowedStatusesForBuilding()
    {
        return $this->allowedStatusesForBuilding;
    }

    public function getAccessibleBuildingIds()
    {
        $userId = request()->user()->id;
        $token = request()->attributes->get('token');

        $organization_id = $token['organization_id'];
        $role_name = $token['role_name'];

        if ($role_name === 'Manager') {
            return ManagerBuilding::where('user_id', $userId)->pluck('building_id')->toArray();
        }
        elseif ($role_name === 'Staff') {
            $staffRecord = StaffMember::where('user_id', $userId)->first();
            return [$staffRecord->building_id];
        }

        return Building::where('organization_id', $organization_id)->pluck('id')->toArray();
    }

    public function checkBuildingAccess($buildingId)
    {
        $user = request()->user();
        $token = request()->attributes->get('token');
        $organization_id = $token['organization_id'] ?? null;
        $role_name = $token['role_name'] ?? null;

        if (
            $buildingId &&
            $role_name === 'Manager' &&
            !ManagerBuilding::where('building_id', $buildingId)
                ->where('user_id', $user->id)
                ->exists()
        ) {
            return [
                'access' => false,
                'message' => 'Access denied to this building'
            ];
        }
        elseif ($role_name === 'Staff') {
            $staffRecord = StaffMember::where('user_id', $user->id)
                ->where('building_id', $buildingId)
                ->first();

            if (!$staffRecord) {
                return [
                    'access' => false,
                    'message' => 'Access denied to this building'
                ];
            }
        }


        return [
            'access' => true,
            'organization_id' => $organization_id
        ];
    }

    public function departments()
    {
        $token = request()->attributes->get('token');
        $organization_id = $token['organization_id'] ?? null;

        return Department::where('organization_id', $organization_id)
            ->orderBy('name', 'asc')
            ->select('id', 'name')
            ->get();
    }

    public function buildings($buildingIds)
    {
        return Building::whereIn('id', $buildingIds)
            ->orderBy('name', 'asc')
            ->select('id', 'name')
            ->get();
    }

    public function organizationBuildings()
    {
        $token = request()->attributes->get('token');
        $organization_id = $token['organization_id'];

        return Building::where('organization_id', $organization_id)
            ->select('id', 'name')
            ->orderBy('name', 'asc')
            ->get();
    }

    public function managerBuildings($managerStaffId)
    {
        return ManagerBuilding::where('staff_id', $managerStaffId)
            ->with(['building:id,name'])
            ->get();
    }
    
    public function approvedBuildings($buildingIds)
    {
        return Building::whereIn('id', $buildingIds)
            ->whereIn('status', ['Approved', 'For Re-Approval'])
            ->orderBy('name', 'asc')
            ->select('id', 'name')
            ->get();
    }

    public function levels($buildingIds)
    {
        return BuildingLevel::whereIn('building_id', $buildingIds)
            ->orderBy('level_name', 'asc')
            ->select('id', 'level_name')
            ->get();
    }

    public function units($buildingIds)
    {
        return BuildingUnit::whereIn('building_id', $buildingIds)
            ->orderBy('unit_name', 'asc')
            ->select('id', 'unit_name')
            ->get();
    }

    public function rentedOrSoldUnits($buildingIds)
    {
        return BuildingUnit::select('id', 'unit_name')
            ->where('availability_status', '!=', 'Available')
            ->where('status', 'Approved')
            ->whereIn('building_id', $buildingIds)
            ->orderBy('unit_name', 'asc')
            ->select('id', 'unit_name')
            ->get();
    }

    public function membershipsUnits($buildingIds)
    {
        return BuildingUnit::whereIn('building_id', $buildingIds)
            ->where('status', 'Approved')
            ->where('availability_status', 'Available')
            ->where('sale_or_rent', 'Not Available')
            ->whereNotIn('unit_type', ['Room', 'Shop', 'Apartment'])
            ->orderBy('unit_name', 'asc')
            ->select('id', 'unit_name')
            ->get();
    }

    public function memberships($buildingIds)
    {
        return Membership::whereIn('building_id', $buildingIds)
            ->orderBy('name', 'asc')
            ->select('id', 'name')
            ->get();
    }

    public function users(bool $excludeAuthenticatedUser = true, array $notIn = [])
    {
        $query = User::query()
            ->orderBy('name', 'asc')
            ->select('id', 'name', 'email');

        if ($excludeAuthenticatedUser) {
            $authenticatedUserId = request()->user()->id;
            $query->where('id', '!=', $authenticatedUserId);
        }

        if (!empty($notIn)) {
            $query->whereNotIn('id', $notIn);
        }

        return $query->get();
    }

    public function availableUnitsOfBuilding($buildingId)
    {
        return BuildingUnit::select('id', 'unit_name')
            ->where('sale_or_rent', '!=', 'Not Available')
            ->where('availability_status', 'Available')
            ->where('status', 'Approved')
            ->where('building_id', $buildingId)
            ->orderBy('unit_name', 'asc')
            ->select('id', 'unit_name')
            ->get();
    }

    public function specificTypesOfUnitsOfBuilding($buildingId, $unit_type)
    {
        return BuildingUnit::select('id', 'unit_name')
            ->where('sale_or_rent', 'Not Available')
            ->whereIn('unit_type', $unit_type)
            ->where('status', 'Approved')
            ->where('building_id', $buildingId)
            ->orderBy('unit_name', 'asc')
            ->get();
    }

}
