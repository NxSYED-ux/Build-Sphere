<?php

namespace App\Http\Controllers\AppControllers;

use App\Http\Controllers\Controller;
use App\Models\BuildingUnit;
use App\Models\Department;
use App\Models\Query;
use App\Models\QueryPicture;
use App\Models\StaffMember;
use App\Models\UserBuildingUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QueryController extends Controller
{
    public function userUnitNames(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['error' => 'User not authenticated.'], 401);
            }

            $userUnits = UserBuildingUnit::where('user_id', $user->id)
                ->with(['unit:id,unit_name,organization_id'])
                ->get();

            $formattedUnits = $userUnits->map(function ($unit) {
                return [
                    'id' => $unit->unit->id,
                    'unit_name' => $unit->unit->unit_name,
                    'organization_id' => $unit->unit->organization_id,
                ];
            });

            return response()->json(['units' => $formattedUnits], 200);
        } catch (\Exception $e) {
            Log::error("Error in userUnitNames: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching user unit names.'], 500);
        }
    }

    public function correspondingDepartments($organizationId)
    {
        try {
            if (!is_numeric($organizationId)) {
                return response()->json(['error' => 'Invalid Organization ID'], 400);
            }

            $departments = Department::where('organization_id', $organizationId)
                ->select('id', 'name')
                ->get();

            return response()->json(['departments' => $departments], 200);
        } catch (\Exception $e) {
            Log::error('Error in correspondingDepartments: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    public function logQuery(Request $request)
    {
        $request->validate([
            'departmentId' => 'required|integer',
            'UnitId' => 'required|integer',
            'description' => 'required|string',
            'picture.*' => 'nullable|image|max:2048',
        ]);

        $userId = $request->user()->id ?? null;
        if (!$userId) return response()->json(['error' => 'User ID is required'], 400);

        DB::beginTransaction();
        try {
            $building = BuildingUnit::where('id', $request->UnitId)->select('building_id')->first();
            if (!$building) throw new \Exception('Invalid Unit.');

            $staffMember = StaffMember::where([
                'department_id' => $request->departmentId,
                'building_id' => $building->building_id,
                'status' => 1,
                'accept_queries' => 1
            ])
                ->select('id', 'active_load')
                ->orderBy('active_load', 'asc')
                ->lockForUpdate()
//                ->skipLocked()  // Skip Locked is not in the Laravel
                ->first();

            if (!$staffMember) throw new \Exception('No available staff member, try again later.');

            $query = Query::create([
                'user_id' => $userId,
                'unit_id' => $request->UnitId,
                'building_id' => $building->building_id,
                'department_id' => $request->departmentId,
                'staff_member_id' => $staffMember->id,
                'description' => $request->description,
                'status' => 'Open',
            ]);

            $staffMember->increment('active_load');

            if ($request->hasFile('picture')) {
                foreach ($request->file('picture') as $picture) {
                    $imageName = time() . '_' . $picture->getClientOriginalName();
                    $imagePath = 'uploads/queries/images/' . $imageName;
                    $picture->move(public_path('uploads/queries/images'), $imageName);
                    QueryPicture::create([ 'query_id' => $query->id, 'file_path' => $imagePath, 'file_name' => $imageName ]);
                }
            }

            DB::commit();
            return response()->json(['message' => 'Query logged successfully', 'query' => $query], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in logQuery: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage() ?: 'Failed to assign query, try again later.'], 500);
        }
    }

    public function getUserQueries(Request $request){
        return $this->getQueriesByField($request,'user_id');
    }

    public function getStaffQueries(Request $request){
        return $this->getQueriesByField($request,'staff_member_id');
    }

    private function getQueriesByField(Request $request, $field)
    {
        try {
            $user = $request->user() ?? null;
            if (!$user) {
                return response()->json(['error' => 'User not authenticated.'], 401);
            }

            $userId = null;

            if ($field === 'user_id') {
                $userId = $user->id;
            } elseif ($field === 'staff_member_id') {
                $staffData = StaffMember::where('user_id', $user->id)->first();

                if (!$staffData) {
                    return response()->json(['error' => 'Only staff members can access this.'], 400);
                }

                $userId = $staffData->id;
            } else {
                return response()->json(['error' => 'Invalid field parameter.'], 400);
            }

            $statuses = $request->query('statuses');

            if (!$statuses || trim($statuses) === '') {
                return response()->json(['error' => 'Statuses are required.'], 400);
            }

            $statusArray = explode(',', $statuses);

            $queries = Query::where($field, $userId)
                ->whereIn('status', $statusArray)
                ->select(['id', 'description', 'status', 'expected_closure_date', 'remarks', 'created_at'])
                ->with([
                    'unit:id,unit_name',
                    'building:id,name',
                    'building.pictures:id,building_id,file_path'
                ])
                ->orderBy('created_at', 'DESC')
                ->get();

            return response()->json(['queries' => $queries], 200);
        } catch (\Exception $e) {
            Log::error("Error in getQueriesByField: " . $e->getMessage());
            return response()->json(['error' => 'Failed to retrieve queries.'], 500);
        }
    }

    public function getQueryDetails(Request $request, $id)
    {
        try {
            if (!is_numeric($id)) {
                return response()->json(['error' => 'Invalid query ID'], 400);
            }
            $query = Query::where('id', $id)
                ->select(['id', 'description', 'status', 'expected_closure_date', 'remarks', 'created_at', 'unit_id'])
                ->with(['pictures:query_id,file_path'])
                ->with(['unit:id,unit_name'])
                ->first();

            if (!$query) {
                return response()->json(['error' => 'Query not found'], 404);
            }

            return response()->json(['query' => $query], 200);
        } catch (\Exception $e) {
            Log::error("Error in getQueryDetails: " . $e->getMessage());
            return response()->json(['error' => 'Failed to retrieve query details.'], 500);
        }
    }

    public function acceptQuery(Request $request){
        return $this->acceptOrRejectQuery($request,'Accepted');
    }

    public function rejectQuery(Request $request){
        return $this->acceptOrRejectQuery($request,'Rejected');
    }

    public function acceptOrRejectQuery(Request $request, $status)
    {
        try {
            $user = $request->user() ?? null;
            if (!$user) {
                return response()->json(['error' => 'User ID is required'], 400);
            }

            $request->validate([
                'id' => 'required|integer|exists:queries,id',
                'date' => 'required',
                'remarks' => $status === 'Rejected' ? 'required|string|min:5' : 'nullable|string',
            ]);

            $staffData = StaffMember::where('user_id', $user->id)->first();
            if (!$staffData) {
                return response()->json(['error' => 'Only staff members can update queries'], 403);
            }

            $updatedRows = Query::where('id', $request->id)
                ->where('status', 'Open')
                ->where('staff_member_id', $staffData->id)
                ->update([
                    'status' => $status === 'Rejected' ? 'Rejected' : 'In Progress',
                    'remarks' => $status === 'Rejected' ? $request->remarks : null,
                    'expected_closure_date' => $request->date,
                ]);

            if ($updatedRows === 0) {
                return response()->json(['error' => 'Unable to update status: The query ID may be invalid or the status is already changed'], 400);
            }

            return response()->json(['message' => 'Status changed successfully'], 200);
        } catch (\Exception $e) {
            Log::error("Error in acceptOrRejectQuery: " . $e->getMessage());
            return response()->json(['error' => 'Failed to update query status'], 500);
        }
    }

}
