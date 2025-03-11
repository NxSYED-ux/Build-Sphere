<?php

namespace App\Http\Controllers\AppControllers;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrganizationDetailsController extends Controller
{
    public function organizationDetails(Request $request, $id)
    {
        try {
            if (!is_numeric($id)) {
                return response()->json(['error' => 'Invalid organization ID'], 400);
            }

            $organization = Organization::where('id', $id)
                ->select('id', 'name')
                ->with(['pictures' => function ($query) {
                    $query->select('organization_id', 'file_path');
                }])
                ->first();

            if (!$organization) {
                return response()->json(['error' => 'Organization not found'], 404);
            }

            $buildings = Building::where('organization_id', $id)
                ->whereIn('status', ['Approved', 'For Reapproval'])
                ->select('id', 'name', 'address_id')
                ->with([
                    'address:id,location,city,province,country',
                    'pictures:building_id,file_path'
                ])
                ->orderBy('updated_at', 'DESC')
                ->get();

            return response()->json([
                'organization' => $organization,
                'buildings' => $buildings,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching organization details: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching organization details.'], 500);
        }
    }

}
