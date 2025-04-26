<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Jobs\BuildingNotifications;
use App\Models\Building;
use App\Models\BuildingLevel;
use App\Models\Department;
use App\Models\ManagerBuilding;
use App\Notifications\DatabaseOnlyNotification;
use App\Notifications\UserNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        try {
            $token = $request->attributes->get('token');

            $departments = collect();
            if (!empty($token['organization_id'])) {
                $departments = Department::where('organization_id', $token['organization_id'])->get();
            }

            return view('Heights.Owner.Departments.index', compact('departments'));

        } catch (\Exception $e) {
            Log::error('Department Index Error: ' . $e->getMessage());
            return back()->with('error', 'Something went wrong! Please try again.');
        }
    }

    public function store(Request $request)
    {
        $user = $request->user() ?? abort(403, 'Unauthorized action.');
        $token = $request->attributes->get('token');
        if (empty($token['organization_id'])) {
            return redirect()->back()->with('error', "You can't perform this action.");
        }
        $organization_id = $token['organization_id'];

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('departments')->where(function ($query) use ($organization_id) {
                    return $query->where('organization_id', $organization_id);
                }),
            ],
            'description' => 'nullable|string',
        ], [
            'name.unique' => 'This department name is already in use.',
        ]);

        try {
            $department = Department::create([
                'name' => $request->name,
                'description' => $request->description,
                'organization_id' => $organization_id,
            ]);

            $user->notify(new DatabaseOnlyNotification(
                null,
                "New Department Created",
                "The department '{$request->level_name}' has been successfully created.",
                ['web' => "owner/departments/{$department->id}"]
            ));

            return redirect()->back()->with('success', 'Departemnt created successfully.');
        } catch (\Exception $e) {
            Log::error('Error in Department store: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong! Please try again.');
        }
    }

    public function show(Request $request, Department $department)
    {
        $token = $request->attributes->get('token');

        if (empty($token['organization_id'])) {
            return redirect()->back()->with('error', "Access denied. Organization information is missing.");
        }

        $organization_id = $token['organization_id'];

        if ($department->organization_id != $organization_id) {
            return redirect()->back()->with('error', "The requested department was not found.");
        }

        $staffMembers = $department->staffMembers()
            ->with('Users')
            ->paginate(10);

        return view('Owner.Departments.show', compact('department', 'staffMembers'));
    }

    public function edit(Request $request,Department $department)
    {
        $token = $request->attributes->get('token');

        if (empty($token['organization_id'])) {
            return redirect()->back()->with('error', "Access denied. Organization information is missing.");
        }

        $organization_id = $token['organization_id'];

        if ($department->organization_id != $organization_id) {
            return redirect()->back()->with('error', "The requested department was not found.");
        }

        try {
            return response()->json([
                'department' => $department,
            ]);

        } catch (\Exception $e) {
            Log::error('Error in ownerEdit: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching building level data.'], 500);
        }
    }

    public function destroy(Request $request)
    {
        $user = $request->user() ?? abort(403, 'Unauthorized');
        $token = $request->attributes->get('token');

        if (empty($token['organization_id'])) {
            return redirect()->back()->with('error', "Access denied. Organization information is missing.");
        }
        $organization_id = $token['organization_id'];

        $request->validate([
            'id' => 'required|exists:departments,id',
        ]);

        try {
            $department = Department::where([
                ['id', '=', $request->id],
                ['organization_id', '=', $organization_id],
            ])->first();

            if (!$department) {
                return redirect()->back()->with('error', "The department you are trying to delete was not found.");
            }

            if ($department->staffMembers()->count() > 0) {
                return redirect()->back()->with('error', "This department cannot be deleted because it has assigned staff members.");
            }

            $departmentName = $department->name;
            $department->delete();

            $user->notify(new DatabaseOnlyNotification(
                null,
                "Department Deleted Successfully",
                "The department '{$departmentName}' has been successfully deleted.",
                ['web' => "owner/departments"]
            ));

            return redirect()->back()->with('success', 'Department deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting department: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again later.');
        }
    }
}
