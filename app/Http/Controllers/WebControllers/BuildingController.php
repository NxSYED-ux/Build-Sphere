<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Jobs\BuildingNotifications;
use App\Models\Address;
use App\Models\Building;
use App\Models\BuildingDocument;
use App\Models\BuildingLevel;
use App\Models\BuildingPicture;
use App\Models\BuildingUnit;
use App\Models\DropdownType;
use App\Models\ManagerBuilding;
use App\Models\Membership;
use App\Models\Organization;
use App\Models\PlanSubscriptionItem;
use App\Services\AdminFiltersService;
use App\Services\OwnerFiltersService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class BuildingController extends Controller
{
    // Index Functions
    public function adminIndex(Request $request)
    {
        try {
            $adminService = new AdminFiltersService();

            $organizations = $adminService->organizations();
            $statuses = $adminService->getAllowedStatusesForBuilding();

            $search = $request->input('search');
            $selectedOrganization = $request->input('organization_id');
            $selectedStatus = $request->input('status');

            $buildingsQuery = Building::with(['pictures', 'address', 'organization'])
                ->whereIn('status', $statuses);

            if ($search) {
                $buildingsQuery->where('name', 'like', '%' . $search . '%');
            }

            if ($selectedOrganization) {
                $buildingsQuery->where('organization_id', $selectedOrganization);
            }

            if ($selectedStatus) {
                $buildingsQuery->where('status', $selectedStatus);
            }

            $buildings = $buildingsQuery->paginate(12);

            return view('Heights.Admin.Buildings.index', compact('buildings', 'organizations', 'statuses'));
        } catch (\Throwable $e) {
            Log::error('Error in adminIndex: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while fetching buildings.');
        }
    }

    public function ownerIndex(Request $request)
    {
        try {
            $ownerService = new OwnerFiltersService();
            $buildingIds = $ownerService->getAccessibleBuildingIds();

            $token = $request->attributes->get('token');
            $organization_id = $token['organization_id'];

            $search = $request->input('search');
            $selectedStatus = $request->input('status');

            $buildingsQuery = Building::with(['pictures', 'address'])
                ->where('organization_id', $organization_id)
                ->whereIn('id', $buildingIds);

            if ($search) {
                $buildingsQuery->where('name', 'like', '%' . $search . '%');
            }

            if ($selectedStatus) {
                $buildingsQuery->where('status', $selectedStatus);
            }

            $buildings = $buildingsQuery->paginate(12);
            $statuses = $ownerService->getAllowedStatusesForBuilding();

            return view('Heights.Owner.Buildings.index', compact('buildings', 'statuses'));

        } catch (\Throwable $e) {
            Log::error('Error in ownerIndex: ' . $e->getMessage());
            return back()->with('error', 'Something went wrong! Please try again.');
        }
    }


    // Create Functions
    public function adminCreate()
    {
        return $this->create('admin');
    }

    public function ownerCreate(Request $request)
    {
        try{
            $user = $request->user();
            $token = $request->attributes->get('token');
            $organization_id = $token['organization_id'];

            $subscriptionLimit = PlanSubscriptionItem::where('organization_id', $organization_id)
                ->where('service_catalog_id', 1)
                ->first();

            if(!$subscriptionLimit) {
                $errorHeading = $user->role_id === 2 ? 'plan_upgrade_error' : 'error';
                $errorMessage = 'The current plan doesn\'t include building management. Please upgrade the plan.';
                return redirect()->back()->with($errorHeading, $errorMessage);
            }

            if($subscriptionLimit->used >= $subscriptionLimit->quantity) {
                $errorHeading = $user->role_id === 2 ? 'plan_upgrade_error' : 'error';
                $errorMessage = 'Building limit reached. Upgrade the organization plan to add more buildings.';
                return redirect()->back()->with($errorHeading, $errorMessage);
            }

            return $this->create('owner');
        } catch (\Throwable $e){
            Log::error('Error in ownerCreate: ' . $e->getMessage());
            return back()->with('error', 'Something went wrong! Please try again.');
        }
    }

    private function create(string $portal)
    {
        try {
            $dropdownData = DropdownType::with(['values.childs.childs'])
                ->where('type_name', 'Country')
                ->get();

            $documentType = DropdownType::with(['values'])
                ->where('type_name', 'Building-document-type')
                ->first();

            $buildingType = DropdownType::with(['values'])
                ->where('type_name', 'Building-type')
                ->first();

            $documentTypes = $documentType ? $documentType->values->pluck('value_name', 'id') : collect();
            $buildingTypes = $buildingType ? $buildingType->values()->where('status', 1)->get() : collect();

            if ($portal == 'admin') {
                $adminService = new AdminFiltersService();
                $organizations = $adminService->organizations();
                return view('Heights.Admin.Buildings.create', compact('organizations', 'dropdownData', 'buildingTypes', 'documentTypes'));
            }
            elseif ($portal == 'owner') {
                return view('Heights.Owner.Buildings.create', compact('dropdownData', 'buildingTypes', 'documentTypes'));
            }
            else {
                abort(404, 'Page not found');
            }
        } catch (\Throwable $e) {
            Log::error('Error in create method: ' . $e->getMessage());
            return back()->with('error', 'Something went wrong! Please try again.');
        }
    }


    // Store Functions
    public function adminStore(Request $request)
    {
        $request->validate([
            'organization_id' => 'required|exists:organizations,id',
        ]);
        return $this->store($request, 'admin',$request->organization_id,'Approved',"This building is added by admin {$request->user()->name}");
    }

    public function ownerStore(Request $request)
    {
        try{
            $token = $request->attributes->get('token');
            $organization_id = $token['organization_id'];

            return $this->store($request, 'owner',$organization_id,'Under Processing',null, $token['role_name']);
        } catch (\Throwable $e) {
            Log::error('Error in owner store buildings: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'An error occurred while creating the building.');
        }
    }

    private function store(Request $request, String $portal, $organization_id, $status, $remarks, $role = null)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:buildings,name',
            'building_type' => 'required|string|max:50',
            'area' => 'required|numeric',
            'construction_year' => 'required|integer|min:1800|max:' . date('Y'),
            'location' => 'required|string|max:255',
            'country' => 'required|string|max:50',
            'province' => 'required|string|max:50',
            'city' => 'required|string|max:50',
            'postal_code' => 'required|string|max:50',
            'building_pictures.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',

            'documents.*.type' => 'nullable|string|distinct',
            'documents.*.issue_date' => 'nullable|date',
            'documents.*.expiry_date' => 'nullable|date|after:documents.*.issue_date',
            'documents.*.files' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5048',
        ]);

        DB::beginTransaction();

        try {
            $user = $request->user();

            $address = Address::create([
                'location' => $request->location,
                'country' => $request->country,
                'province' => $request->province,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
            ]);

            $building = Building::create([
                'name' => $request->name,
                'building_type' => $request->building_type,
                'address_id' => $address->id,
                'area' => $request->area,
                'remarks' => $remarks,
                'status' => $status,
                'construction_year' => $request->construction_year,
                'organization_id' => $organization_id,
            ]);

            if ($request->hasFile('building_pictures')) {
                foreach ($request->file('building_pictures') as $image) {
                    $imageName = time() . '_' . $image->getClientOriginalName();
                    $imagePath = 'uploads/buildings/images/' . $imageName;
                    $image->move(public_path('uploads/buildings/images'), $imageName);

                    BuildingPicture::create([
                        'building_id' => $building->id,
                        'file_path' => $imagePath,
                        'file_name' => $imageName,
                    ]);
                }
            }

            foreach ($request->documents ?? [] as $document) {
                if (isset($document['files'])) {
                    $file = $document['files'];
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = 'uploads/buildings/document/' . $document['type'] . '/' . $fileName;
                    $file->move(public_path('uploads/buildings/document/' . $document['type']), $fileName);

                    BuildingDocument::create([
                        'building_id' => $building->id,
                        'document_type' => $document['type'],
                        'issue_date' => $document['issue_date'],
                        'expiry_date' => $document['expiry_date'],
                        'file_path' => $filePath,
                        'file_name' => $fileName,
                    ]);
                }
            }

            $subscriptionLimit = PlanSubscriptionItem::where('organization_id', $organization_id)
                ->where('service_catalog_id', 1)
                ->lockForUpdate()
                ->first();

            if(!$subscriptionLimit) {
                DB::rollBack();
                $errorHeading = $user->role_id === 2 ? 'plan_upgrade_error' : 'error';
                $errorMessage = 'The current plan doesn\'t include building management. Please upgrade the plan.';
                return redirect()->back()->with($errorHeading, $errorMessage)->withInput();
            }

            if($subscriptionLimit->used >= $subscriptionLimit->quantity) {
                DB::rollBack();
                $errorHeading = $user->role_id === 2 ? 'plan_upgrade_error' : 'error';
                $errorMessage = 'Building limit reached. Upgrade the organization plan to add more buildings.';
                return redirect()->back()->with($errorHeading, $errorMessage)->withInput();
            }

            $subscriptionLimit->increment('used');

            DB::commit();

            $link = "{$portal}/buildings/{$building->id}/show";
            $message = "{$building->name} has been added successfully.";

            if ($portal == 'admin') {

                dispatch(new BuildingNotifications(
                    $organization_id,
                    $building->id,
                    'Building Added by Admin',
                    $message,
                    "owner/buildings/{$building->id}/show",

                    $user->id,
                    'Building Added',
                    $message,
                    $link,

                    true,
                ));
                return redirect()->route('buildings.index')->with('success', 'Building created successfully.');

            }
            elseif ($portal == 'owner') {

                dispatch(new BuildingNotifications(
                    $organization_id,
                    $building->id,
                    "Building Added by {$role} ({$request->user()->name})",
                    $message,
                    $link,

                    $user->id,
                    "Building Added",
                    $message,
                    $link,
                ));
                return redirect()->route('owner.buildings.index')->with('success', 'Building created successfully.');

            }
            else {
                abort(404, 'Page not found');
            }

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error in store buildings: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'An error occurred while creating the building.');
        }
    }


    // Show Functions
    public function adminShow($id)
    {
        try {
            $adminService = new AdminFiltersService();
            $result = $adminService->checkBuildingAccess($id);

            if (!$result['access']) {
                return redirect()->back()->with('error', $result['message']);
            }

            return $this->show('admin', $result['building']);

        } catch (\Throwable $e) {
            Log::error('Error in adminShow: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function ownerShow(Request $request, $id)
    {
        try {
            $building = $this->validateOwnerBuildingAccess($request, $id);
            if(!$building instanceof Building){
                return $building;
            }
            return $this->show('owner', $building);

        } catch (\Throwable $e) {
            Log::error('Error in ownerShow: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    private function show(string $portal, Building $building)
    {
        try {
            $building->load([
                'address',
                'pictures',
                'organization.owner',
                'levels.units.pictures'
            ]);

            $owner = $building->organization->owner ?? null;
            $levels = $building->levels ?? collect();
            $units = $levels->flatMap->units ?? collect();

            if ($portal === 'admin') {
                return view('Heights.Admin.Buildings.show', compact('building', 'levels', 'units', 'owner'));
            }

            if ($portal === 'owner') {
                $memberships = Membership::where('building_id', $building->id)->count();
                return view('Heights.Owner.Buildings.show', compact('building', 'levels', 'units', 'owner', 'memberships'));
            }

            return redirect()->back()->with('error', 'Invalid portal access.');

        } catch (\Throwable $e) {
            Log::error('Error in show building: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while retrieving building details.');
        }
    }



    // Edit Functions
    public function adminEdit($id)
    {
        try {
            $adminService = new AdminFiltersService();
            $result = $adminService->checkBuildingAccess($id);

            if (!$result['access']) {
                return redirect()->back()->with('error', $result['message']);
            }

            return $this->edit('admin', $result['building']);
        } catch (\Throwable $e) {
            Log::error('Error in adminEdit: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function ownerEdit(Request $request, $id)
    {
        try {
            $building = $this->validateOwnerBuildingAccess($request, $id);
            if(!$building instanceof Building){
                return $building;
            }
            return $this->edit('owner',$building);
        } catch (\Throwable $e) {
            Log::error('Error in ownerEdit: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    private function edit(string $portal, Building $building)
    {
        try {
            $building->load(['address', 'pictures', 'documents']);

            $dropdownData = DropdownType::with(['values.childs.childs'])
                ->where('type_name', 'Country')
                ->get();

            $documentType = DropdownType::with(['values'])
                ->where('type_name', 'Building-document-type')
                ->first();

            $buildingType = DropdownType::with(['values'])
                ->where('type_name', 'Building-type')
                ->first();

            $documentTypes = $documentType ? $documentType->values->pluck('value_name', 'id') : collect();
            $buildingTypes = $buildingType ? $buildingType->values()->where('status', 1)->get() : collect();

            $view = $portal == 'admin' ? 'Heights.Admin.Buildings.edit' : 'Heights.Owner.Buildings.edit';
            return view($view, compact('building', 'dropdownData', 'buildingTypes', 'documentTypes'));

        } catch (\Throwable $e) {
            Log::error('Error in create method: ' . $e->getMessage());
            return back()->with('error', 'Something went wrong! Please try again.');
        }
    }


    // Update Functions
    public function adminUpdate(Request $request)
    {
        return $this->update($request, 'admin');
    }

    public function ownerUpdate(Request $request)
    {
        $token = $request->attributes->get('token');
        return $this->update($request, 'owner', $token['organization_id'], $token['role_name']);
    }

    private function update(Request $request, String $portal, $organization_id = null, $role = null)
    {
        $request->validate([
            'id' => 'required|exists:buildings,id',
            'name' => 'required|string|max:255|unique:buildings,name,'. $request->id . ',id',
            'building_type' => 'required|string|max:50',
            'area' => 'required|numeric',
            'construction_year' => 'required|integer|min:1800|max:' . date('Y'),
            'location' => 'required|string|max:255',
            'country' => 'required|string|max:50',
            'province' => 'required|string|max:50',
            'city' => 'required|string|max:50',
            'postal_code' => 'required|string|max:50',
            'building_pictures.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'updated_at' => 'required',

            'documents.*.type' => 'nullable|string|distinct',
            'documents.*.issue_date' => 'nullable|date',
            'documents.*.expiry_date' => 'nullable|date|after:documents.*.issue_date',
            'documents.*.files' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5048',
        ]);


        DB::beginTransaction();

        try {
            $user = $request->user();

            $building = Building::where([
                ['id', '=', $request->id],
                ['updated_at', '=', $request->updated_at]
            ])->sharedLock()->first();

            if (!$building) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Please refresh the page and try again.');
            }

            if($portal === 'owner' && $organization_id !== $building->organization_id){
                DB::rollBack();
                return redirect()->back()->with('error', 'The selected building id is invalid.');
            }

            $address = Address::findOrFail($building->address_id);

            $address->update([
                'location' => $request->location,
                'country' => $request->country,
                'province' => $request->province,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
            ]);

            $building->update([
                'name' => $request->name,
                'building_type' => $request->building_type,
                'area' => $request->area,
                'construction_year' => $request->construction_year,
                'updated_at' => now(),
            ]);

            if ($request->hasFile('building_pictures')) {
                foreach ($request->file('building_pictures') as $image) {
                    $imageName = time() . '_' . $image->getClientOriginalName();
                    $imagePath = 'uploads/buildings/images/' . $imageName;
                    $image->move(public_path('uploads/buildings/images'), $imageName);

                    BuildingPicture::create([
                        'building_id' => $building->id,
                        'file_path' => $imagePath,
                        'file_name' => $imageName,
                    ]);
                }
            }

            foreach ($request->documents ?? [] as $document) {
                if (isset($document['files'])) {
                    $file = $document['files'];
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = 'uploads/buildings/document/' . $document['type'] . '/' . $fileName;
                    $file->move(public_path('uploads/buildings/document/' . $document['type']), $fileName);

                    BuildingDocument::create([
                        'building_id' => $building->id,
                        'document_type' => $document['type'],
                        'issue_date' => $document['issue_date'],
                        'expiry_date' => $document['expiry_date'],
                        'file_path' => $filePath,
                        'file_name' => $fileName,
                    ]);
                }
            }

            DB::commit();

            $link = "{$portal}/buildings/{$building->id}/show";
            $message = "{$building->name} has been updated successfully.";

            if ($portal == 'admin') {
                $organization_id = $organization_id ?? $building->organization_id;

                dispatch(new BuildingNotifications(
                    $organization_id,
                    $building->id,
                    'Building updated by Admin',
                    $message,
                    "owner/buildings/{$building->id}/show",

                    $user->id,
                    'Building updated',
                    $message,
                    $link,

                    true,
                ));
                return redirect()->route('buildings.index')->with('success', 'Building updated successfully.');

            } elseif ($portal == 'owner') {

                dispatch(new BuildingNotifications(
                    $organization_id,
                    $building->id,
                    "Building updated by {$role} {$user->name}",
                    $message,
                    $link,

                    $user->id,
                    "Building updated",
                    $message,
                    $link,
                ));
                return redirect()->route('owner.buildings.index')->with('success', 'Building updated successfully.');

            } else {
                abort(404, 'Page not found');
            }

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error in update method: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Something went wrong! Please try again.');
        }
    }


    // status Functions
    public function submitBuilding(Request $request)
    {
        $request->validate([
            'building_id' => 'required|integer|exists:buildings,id',
        ]);

        try {
            $user = $request->user();
            $token = $request->attributes->get('token');
            $organization_id = $token['organization_id'];

            $building = $this->validateOwnerBuildingAccess($request, $request->building_id);

            if(!$building instanceof Building){
                return $building;
            }

            if($building->status !== 'Under Processing'){
                return redirect()->back()->with('error', 'This building cannot be submitted. It is already under review or approved.');
            }

            $building->update([
                'status' => 'Under Review',
                'review_submitted_at' => now(),
            ]);

            $message = "{$building->name} has been submitted successfully for approval to Admin";

            dispatch(new BuildingNotifications(
                $organization_id,
                $building->id,
                "Building Submitted by {$token['role_name']} ({$user->name})",
                $message,
                "owner/buildings/{$building->id}/show",

                $user->id,
                "Building Submitted",
                $message,
                "owner/buildings/{$building->id}/show",

                true,
                'New Building for approval',
                "{$building->name} is available for approval",
                "admin/buildings/{$building->id}/show",
            ));

            return redirect()->back()->with('success', 'Building Submitted successfully.');
        } catch (\Throwable $e) {
            Log::error('Error in submit building: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong! Please try again.');
        }
    }

    public function approvalReminder(Request $request)
    {

        $request->validate([
            'building_id' => 'required|integer|exists:buildings,id',
        ]);

        try {
            $user = $request->user();
            $token = $request->attributes->get('token');
            $organization_id = $token['organization_id'];

            $building = $this->validateOwnerBuildingAccess($request, $request->building_id);

            if(!$building instanceof Building){
                return $building;
            }

            if(!($building->status === 'Under Review' || $building->status === 'For Re-Approval')){
                return redirect()->back()->with('error', 'This building has already been approved or rejected. Approval reminders are only allowed for buildings under review.');
            }

            dispatch(new BuildingNotifications(
                $organization_id,
                $building->id,
                "Reminder Sent by {$token['role_name']} ({$user->name})",
                "Admin has been reminded successfully for {$building->name}",
                "owner/buildings/{$building->id}/show",

                $user->id,
                "Reminder Sent",
                "Admin has been reminded successfully for {$building->name}",
                "owner/buildings/{$building->id}/show",

                true,
                'Approval Reminder',
                "You have been reminded to review and approve the pending request of {$building->name}",
                "admin/buildings/{$building->id}/show",
            ));

            return redirect()->back()->with('success', 'Admin Remaindered successfully.');
        }catch (\Exception $e) {
            Log::error('Error in submit building: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong! Please try again.');
        }
    }

    public function approveBuilding(Request $request)
    {
        $request->validate([
            'building_id' => 'required|integer|exists:buildings,id',
            'remarks' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $user = $request->user();

            $adminService = new AdminFiltersService();
            $result = $adminService->checkBuildingAccess($request->building_id);

            if (!$result['access']) {
                DB::rollBack();
                return redirect()->back()->with('error', $result['message']);
            }

            $building = $result['building'];

            if(!($building->status === 'Under Review' || $building->status === 'For Re-Approval')){
                DB::rollBack();
                return redirect()->back()->with('error', 'This building cannot be approved because it is not currently under review or up for re-approval.');
            }

            $building->update([
                'status' => 'Approved',
                'remarks' => $request->remarks ?? null,
                'approved_at' => now(),
            ]);

            $building->levels()->update(['status' => 'Approved']);
            $building->units()->update(['status' => 'Approved']);

            DB::commit();

            $message = "{$building->name} has been approved successfully";

            dispatch(new BuildingNotifications(
                $building->organization_id,
                $building->id,
                "Building approved by Admin",
                $message,
                "owner/buildings/{$building->id}/show",

                $user->id,
                "Building approved",
                $message,
                "admin/buildings/{$building->id}/show",

                true,
            ));

            return redirect()->back()->with('success', 'Building Approved Successfully.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error in approving building: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong! Please try again.');
        }
    }

    public function rejectBuilding(Request $request)
    {
        $request->validate([
            'building_id' => 'required|integer|exists:buildings,id',
            'remarks' => 'required|string',
        ]);

        try {
            $user = $request->user();

            $adminService = new AdminFiltersService();
            $result = $adminService->checkBuildingAccess($request->building_id);

            if (!$result['access']) {
                return redirect()->back()->with('error', $result['message']);
            }

            $building = $result['building'];

            if($building->status !== 'Under Review'){
                return redirect()->back()->with('error', 'Only buildings currently under review can be rejected.');
            }

            $building->update([
                'status' => 'Rejected',
                'remarks' => $request->remarks,
                'rejected_at' => now(),
            ]);

            dispatch(new BuildingNotifications(
                $building->organization_id,
                $building->id,
                "Building rejected by Admin",
                "{$building->name} has been rejected by admin with remarks {$request->remarks}",
                "owner/buildings/{$building->id}/show",

                $user->id,
                "Building rejected",
                "{$building->name} has been rejected successfully",
                '',

                true,
            ));

            return redirect()->route('buildings.index')->with('success', 'Building rejected successfully.');
        }catch (\Exception $e) {
            Log::error('Error in submit building: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong! Please try again.');
        }
    }

    public function reportBuildingRemarks(Request $request)
    {
        $request->validate([
            'building_id' => 'required|integer|exists:buildings,id',
            'remarks' => 'required|string',
        ]);

        try {
            $user = $request->user();

            $adminService = new AdminFiltersService();
            $result = $adminService->checkBuildingAccess($request->building_id);

            if (!$result['access']) {
                return redirect()->back()->with('error', $result['message']);
            }

            $building = $result['building'];

            if ($building->status !== 'For Re-Approval') {
                return redirect()->back()->with('error', 'Remarks can only be reported for buildings under re-approval.');
            }

            dispatch(new BuildingNotifications(
                $building->organization_id,
                $building->id,
                "Remarks on {$building->name} from Admin",
                "The admin has provided remarks for your building, {$building->name}: {$request->remarks}. Please review and take necessary action to proceed with the approval.",
                "owner/buildings/{$building->id}/show",

                $user->id,
                "Remarks Added Successfully",
                "The remark '{$request->remarks}' has been added for the owner of '{$building->name}' to take the necessary actions.",
                '',

                true,
            ));

            return redirect()->back()->with('success', 'Remarks Reported successfully.');
        }catch (\Throwable $e) {
            Log::error('Error in report Building Remarks: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong! Please try again.');
        }
    }


    // Building Tree
    public function tree(Request $request)
    {
        try {
            $building = null;
            $levels = null;
            $units = null;
            $owner = null;
            $buildingId = $request->input('building_id');

            $ownerService = new OwnerFiltersService();

            $buildingIds = $ownerService->getAccessibleBuildingIds();
            $buildingsDropDown = $ownerService->buildings($buildingIds);
            $buildingId = $buildingId ?? $buildingsDropDown->first()?->id;

            if (!$buildingId || !isset($buildingsDropDown->keyBy('id')[$buildingId])) {
                if (!$buildingId) {
                    return view('Heights.Owner.Buildings.tree', compact('building', 'levels', 'units', 'owner', 'buildingsDropDown'));
                }
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
        } catch (\Throwable $e) {
            Log::error('Error in Building Tree : ' . $e->getMessage());
            return back()->with('error', 'An error occurred, while loading the page.');
        }
    }


    // For Report
    public function getBuildingDetails(Request $request)
    {
        try {
            $buildingId = $request->input('building');

            if (!$buildingId) {
                return response()->json([
                    'message' => 'Building ID is required.'
                ], 400);
            }

            $building = Building::with('address', 'pictures')->find($buildingId);

            if (!$building) {
                return response()->json([
                    'message' => 'Building not found.'
                ], 404);
            }

            $imageUrl = $building->pictures->first()?->file_path;

            return response()->json([
                'name' => $building->name,
                'building_type' => $building->building_type,
                'status' => $building->status,
                'area' => $building->area,
                'construction_year' => $building->construction_year,
                'city' => $building->address->city ?? 'N/A',
                'image' => $imageUrl,
            ]);
        } catch (\Throwable $e) {
            Log::error('Error in getBuildingDetails: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to retrieve building details.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    // Helper Functions
    private function validateOwnerBuildingAccess(Request $request, $id)
    {
        $token = $request->attributes->get('token');
        $organization_id = $token['organization_id'];

        $building = Building::find($id);

        if (!$building || $organization_id !== $building->organization_id) {
            return redirect()->back()->with('error', 'Invalid Building Id.');
        }

        $ownerService = new OwnerFiltersService();
        $result = $ownerService->checkBuildingAccess($building->id);

        if(!$result['access']){
            return redirect()->back()->with('error', $result['message']);
        }

        return $building;
    }


    // Other Functions
    public function getLevels($buildingId)
    {
        $levels = BuildingLevel::where('building_id', $buildingId)->pluck('level_name', 'id');
        return response()->json(['levels' => $levels]);
    }

    public function destroyImage(string $id)
    {
        $image = BuildingPicture::findOrFail($id);

        if ($image) {
            $oldImagePath = public_path($image->file_path);
            if (File::exists($oldImagePath)) {
                File::delete($oldImagePath);
            }

            $image->delete();
        }

        return response()->json(['success' => true]);
    }

    public function removeDocument($fileId)
    {
        $document = BuildingDocument::find($fileId);

        if ($document) {
            $oldImagePath = public_path($document->file_path);
            if (File::exists($oldImagePath)) {
                File::delete($oldImagePath);
            }

            $document->delete();

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }

    public function getDocument($id)
    {
        $file = BuildingDocument::find($id);
        if ($file) {
            return response()->json(['success' => true, 'document' => $file]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    public function updateDocument(Request $request, $id)
    {
        $file = BuildingDocument::find($id);

        if (!$file) {
            return redirect()->back()->with('error', 'Document not found.')->setStatusCode(404);
        }
        $request->validate([
            'document_type' => 'required|string',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date',
            'file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5048'
        ]);

        try {
            $file->document_type = $request->document_type;
            $file->issue_date = $request->issue_date;
            $file->expiry_date = $request->expiry_date;

            if ($request->hasFile('file')) {
                if (!empty($file->file_path)) {
                    $oldImagePath = public_path($file->file_path);
                    if (File::exists($oldImagePath)) {
                        File::delete($oldImagePath);
                    }
                }

                $newFile = $request->file('file');
                $fileName = time() . '_' . $newFile->getClientOriginalName();
                $filePath = 'uploads/buildings/document/' . $request->document_type . '/' . $fileName;
                $newFile->move(public_path('uploads/buildings/document/' . $request->document_type), $fileName);

                $file->file_path = $filePath;
                $file->file_name = $fileName;
            }

            $file->save();

            return redirect()->back()->with('success', 'Document updated successfully!');
        } catch (Exception $e) {
            Log::error('Error in Update Document' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function getOccupancyStats(Request $request){
        $user = $request->user();
        $token = $request->attributes->get('token');
        $organization_id = $token['organization_id'];
        $role_id = $token['role_id'];

        return $this->orgOccupancyStats($request, $organization_id, $role_id, $user->id, 'user_id');
    }

    public function getManagerBuildingsOccupancyStats(Request $request, string $id)
    {
        $token = $request->attributes->get('token');
        $organization_id = $token['organization_id'];

        return $this->orgOccupancyStats($request, $organization_id, 3, $id, 'staff_id');
    }


    // Helper function
    private function orgOccupancyStats(Request $request, string $organization_id, int $roleId, string $id, string $trackOn)
    {
        $building_id = $request->input('buildingId');

        try {
            $buildingIds = [];
            if ($roleId === 3) {
                $buildingIds = ManagerBuilding::where($trackOn, $id)->pluck('building_id')->toArray();

                if ($building_id && !in_array($building_id, $buildingIds)) {
                    return response()->json(['error' => 'You do not have access to this building.'], 403);
                }
            }

            $units = BuildingUnit::where('organization_id', $organization_id)
                ->when($building_id, function ($query) use ($building_id) {
                    $query->where('building_id', $building_id);
                }, function ($query) use ($roleId, $buildingIds) {
                    if ($roleId === 3) {
                        $query->whereIn('building_id', $buildingIds);
                    }
                })
                ->select('availability_status')
                ->get()
                ->groupBy('availability_status')
                ->map->count();

            return response()->json([
                'availableUnits' => $units['Available'] ?? 0,
                'rentedUnits' => $units['Rented'] ?? 0,
                'soldUnits' => $units['Sold'] ?? 0,
            ], 200);

        } catch (\Throwable $e) {
            Log::error('Error in occupancy chart: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong. Please try again later.'], 500);
        }
    }

}
