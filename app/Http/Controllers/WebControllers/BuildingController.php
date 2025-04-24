<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Jobs\BuildingNotifications;
use App\Models\Address;
use App\Models\Building;
use App\Models\BuildingDocument;
use App\Models\BuildingLevel;
use App\Models\BuildingPicture;
use App\Models\DropdownType;
use App\Models\ManagerBuilding;
use App\Models\Organization;
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
            $search = $request->input('search');

            $buildings = Building::with(['pictures', 'address', 'organization'])
                ->whereNotIn('status', ['Under Processing', 'Rejected'])
                ->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('remarks', 'like', '%' . $search . '%')
                        ->orWhereHas('organization', function ($subQuery) use ($search) {
                            $subQuery->where('name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('address', function ($subQuery) use ($search) {
                            $subQuery->where('city', 'like', '%' . $search . '%');
                        });
                })
                ->paginate(10);


            return view('Heights.Admin.Buildings.index', compact('buildings'));
        } catch (\Exception $e) {
            Log::error('Error in adminIndex: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while fetching buildings.');
        }
    }

    public function ownerIndex(Request $request)
    {
        $user = $request->user() ?? abort(404, 'Page not found');

        try {
            $buildings = null;
            $token = $request->attributes->get('token');

            if (empty($token['organization_id']) || empty($token['role_name'])) {
                return view('Heights.Owner.Buildings.index', compact('buildings'));
            }

            $organization_id = $token['organization_id'];
            $role_name = $token['role_name'];
            $search = $request->input('search');

            $query = Building::with(['pictures', 'address'])
                ->where('organization_id', $organization_id);

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('remarks', 'like', "%{$search}%")
                        ->orWhereHas('address', function ($subQuery) use ($search) {
                            $subQuery->where('city', 'like', "%{$search}%");
                        });
                });
            }

            if ($role_name === 'Manager') {
                $managerBuildingIds = ManagerBuilding::where('user_id', $user->id)->pluck('building_id');
                $query->whereIn('id', $managerBuildingIds);
            }

            $buildings = $query->paginate(10);

            return view('Heights.Owner.Buildings.index', compact('buildings'));

        } catch (\Exception $e) {
            Log::error('Error in ownerIndex: ' . $e->getMessage());
            return back()->with('error', 'Something went wrong! Please try again.');
        }
    }


    // Create Functions
    public function adminCreate()
    {
        return $this->create('admin');
    }

    public function ownerCreate()
    {
        return $this->create('owner');
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
                $organizations = Organization::where('status', 'Enable')->get();
                return view('Heights.Admin.Buildings.create', compact('organizations', 'dropdownData', 'buildingTypes', 'documentTypes'));
            } elseif ($portal == 'owner') {
                return view('Heights.Owner.Buildings.create', compact('dropdownData', 'buildingTypes', 'documentTypes'));
            } else {
                abort(404, 'Page not found');
            }
        } catch (\Exception $e) {
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

            if (!$token || empty($token['organization_id']) || empty($token['role_name'])) {
                return redirect()->back()->withInput()->with('error', 'You cannot perform this action because they are not linked to any organization. Please switch to an organization account to proceed.');
            }

            $organization_id = $token['organization_id'];

            return $this->store($request, 'owner',$organization_id,'Under Processing',null, $token['role_name']);
        }catch (\Exception $e) {
            Log::error('Error in owner store buildings: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'An error occurred while creating the building.');
        }
    }

    private function store(Request $request, String $portal, $organization_id, $status, $remarks, $role = null)
    {
        $user = $request->user() ?? abort(403, 'Unauthorized');

        $request->validate([
            'name' => 'required|string|max:255|unique:buildings,name',
            'building_type' => 'required|string|max:50',
            'area' => 'nullable|numeric',
            'construction_year' => 'nullable|integer|min:1800|max:' . date('Y'),
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'location' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:50',
            'province' => 'nullable|string|max:50',
            'city' => 'nullable|string|max:50',
            'postal_code' => 'nullable|string|max:50',
            'building_pictures.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

            'documents.*.type' => 'nullable|string|distinct',
            'documents.*.issue_date' => 'nullable|date',
            'documents.*.expiry_date' => 'nullable|date|after:documents.*.issue_date',
            'documents.*.files' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5048',
        ]);

        DB::beginTransaction();

        try {

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

            } elseif ($portal == 'owner') {

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

            } else {
                abort(404, 'Page not found');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in store buildings: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'An error occurred while creating the building.');
        }
    }


    // Show Functions
    public function adminShow($id)
    {
        try {
            $building = $this->validateAdminBuildingAccess($id);
            if(!$building instanceof Building){
                return $building;
            }
            return $this->show('admin', $building);
        } catch (\Exception $e) {
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
        } catch (\Exception $e) {
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

            if ($portal == 'admin') {
                return view('Heights.Admin.Buildings.show', compact('building', 'levels', 'units', 'owner'));
            } elseif ($portal == 'owner') {
                return view('Heights.Owner.Buildings.show', compact('building', 'levels', 'units', 'owner'));
            } else {
                abort(404, 'Page not found');
            }
        } catch (\Exception $e) {
            Log::error('Error in show building: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while retrieving building details.');
        }
    }


    // Edit Functions
    public function adminEdit($id)
    {
        try {
            $building = $this->validateAdminBuildingAccess($id);
            if(!$building instanceof Building){
                return $building;
            }
            return $this->edit('admin', $building);
        } catch (\Exception $e) {
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
        } catch (\Exception $e) {
            Log::error('Error in ownerEdit: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    private function edit(string $portal, Building $building)
    {
        try {
            $building->load(['address', 'organization','pictures', 'documents']);

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
                $organizations = Organization::where('status', 'Enable')
                    ->orWhere('id', $building->organization->id)
                    ->get();
                return view('Heights.Admin.Buildings.edit', compact('building','organizations', 'dropdownData', 'buildingTypes', 'documentTypes'));
            } elseif ($portal == 'owner') {
                return view('Heights.Owner.Buildings.edit', compact('building','dropdownData', 'buildingTypes', 'documentTypes'));
            } else {
                abort(404, 'Page not found');
            }
        } catch (\Exception $e) {
            Log::error('Error in create method: ' . $e->getMessage());
            return back()->with('error', 'Something went wrong! Please try again.');
        }
    }


    // Update Functions
    public function adminUpdate(Request $request)
    {
        $request->validate([
            'organization_id' => 'required|exists:organizations,id',
        ]);
        return $this->update($request, 'admin', $request->organization_id);
    }

    public function ownerUpdate(Request $request)
    {
        $token = $request->attributes->get('token');

        if (!$token || empty($token['organization_id']) || empty($token['role_name'])) {
            return redirect()->back()->withInput()->with('error', 'You cannot perform this action because they are not linked to any organization. Please switch to an organization account to proceed.');
        }

        $organization_id = $token['organization_id'];

        return $this->update($request, 'owner', $organization_id, $token['role_name']);
    }

    private function update(Request $request, String $portal, $organization_id, $role = null)
    {
        $user = $request->user() ?? abort(403, 'Unauthorized');
        $token = $request->attributes->get('token');

        $request->validate([
            'id' => 'required|exists:buildings,id',
            'name' => 'required|string|max:255|unique:buildings,name,'. $request->id . ',id',
            'building_type' => 'required|string|max:50',
            'area' => 'nullable|numeric',
            'status' => 'required|string',
            'construction_year' => 'nullable|integer|min:1800|max:' . date('Y'),
            'location' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:50',
            'province' => 'nullable|string|max:50',
            'city' => 'nullable|string|max:50',
            'postal_code' => 'nullable|string|max:50',
            'building_pictures.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'updated_at' => 'required',

            'documents.*.type' => 'nullable|string|distinct',
            'documents.*.issue_date' => 'nullable|date',
            'documents.*.expiry_date' => 'nullable|date|after:documents.*.issue_date',
            'documents.*.files' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5048',
        ]);


        DB::beginTransaction();

        try {
            $building = Building::where([
                ['id', '=', $request->id],
                ['updated_at', '=', $request->updated_at]
            ])->sharedLock()->first();

            if (!$building) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Please refresh the page and try again.');
            }

            if($portal === 'owner' && $token['organization_id'] !== $building->organization_id){
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
                'status' => $request->status,
                'construction_year' => $request->construction_year,
                'organization_id' => $organization_id,
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

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in update method: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'An error occurred while updating the building.');
        }
    }


    // status Functions
    public function submitBuilding(Request $request)
    {
        $user = $request->user() ?? abort(403, 'Unauthorized');
        $token = $request->attributes->get('token');
        $organization_id = $token['organization_id'] ?? null;

        $request->validate([
            'building_id' => 'required|integer|exists:buildings,id',
        ]);

        try {

            $building = $this->validateOwnerBuildingAccess($request,$request->building_id);

            if(!$building instanceof Building){
                return $building;
            }

            $building->update([
                'status' => 'Under Review',
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

            return redirect()->route('owner.buildings.index')->with('success', 'Building Submitted successfully.');
        }catch (\Exception $e) {
            Log::error('Error in submit building: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while updating the building.');
        }
    }

    public function approvalReminder(Request $request){

        $user = $request->user() ?? abort(403, 'Unauthorized');
        $token = $request->attributes->get('token');
        $organization_id = $token['organization_id'] ?? null;

        $request->validate([
            'building_id' => 'required|integer|exists:buildings,id',
        ]);

        try {
            $building = $this->validateOwnerBuildingAccess($request,$request->building_id);

            if(!$building instanceof Building){
                return $building;
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

            return redirect()->route('owner.buildings.index')->with('success', 'Admin Remaindered successfully.');
        }catch (\Exception $e) {
            Log::error('Error in submit building: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while updating the building.');
        }
    }

    public function approveBuilding(Request $request){

        $user = $request->user() ?? abort(403, 'Unauthorized');

        $request->validate([
            'building_id' => 'required|integer|exists:buildings,id',
            'remarks' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $building = $this->validateAdminBuildingAccess($request->building_id);

            if(!$building instanceof Building){
                return $building;
            }

            if($building->status === 'Under Processing'){
                return redirect()->back()->with('error', 'This building is currently under processing and cannot be approved yet.');
            }

            $building->update([
                'status' => 'Approved',
                'remarks' => $request->remarks ?? null,
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

            return redirect()->route('buildings.index')->with('success', 'Building Approved Successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in approving building: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while updating the building.');
        }
    }

    public function rejectBuilding(Request $request){

        $user = $request->user() ?? abort(403, 'Unauthorized');

        $request->validate([
            'building_id' => 'required|integer|exists:buildings,id',
            'remarks' => 'required|string',
        ]);

        try {
            $building = $this->validateAdminBuildingAccess($request->building_id);

            if(!$building instanceof Building){
                return $building;
            }

            if($building->status === 'For Re-Approval'){
                return redirect()->back()->with('error', 'Buildings under re-approval cannot be rejected.');
            }

            $building->update([
                'status' => 'Rejected',
                'remarks' => $request->remarks,
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
            return redirect()->back()->with('error', 'An error occurred while updating the building.');
        }
    }

    public function reportBuildingRemarks(Request $request){

        $user = $request->user() ?? abort(403, 'Unauthorized');

        $request->validate([
            'building_id' => 'required|integer|exists:buildings,id',
            'remarks' => 'required|string',
        ]);

        try {
            $building = $this->validateAdminBuildingAccess($request->building_id);

            if(!$building instanceof Building){
                return $building;
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

            return redirect()->route('buildings.index')->with('success', 'Building rejected successfully.');
        }catch (\Exception $e) {
            Log::error('Error in submit building: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while updating the building.');
        }
    }


    // Helper Functions
    private function validateAdminBuildingAccess($id)
    {
        $allowedStatuses = ['Approved', 'For Re-approval', 'Under Review'];
        $building = Building::find($id);

        if (!$building || !in_array($building->status, $allowedStatuses)) {
            return redirect()->back()->with('error', 'Invalid Building Id.');
        }

        return $building;
    }

    private function validateOwnerBuildingAccess(Request $request, $id)
    {
        $user = $request->user() ?? abort(403, 'Unauthorized');
        $token = $request->attributes->get('token');

        if (empty($token['organization_id']) || empty($token['role_name'])) {
            return redirect()->back()->with('error', 'Invalid Building Id.');
        }

        $organization_id = $token['organization_id'];
        $role_name = $token['role_name'];
        $building = Building::find($id);

        if (!$building || $organization_id !== $building->organization_id) {
            return redirect()->back()->with('error', 'Invalid Building Id.');
        }

        if ($role_name === 'Manager' && !ManagerBuilding::where([
                'building_id' => $building->id,
                'user_id' => $user->id
            ])->exists()) {
            return redirect()->back()->with('error', 'Invalid Building Id.');
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
}
