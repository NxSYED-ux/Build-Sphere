<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Jobs\OrganizationOwnerNotifications;
use App\Jobs\SendRoleNotification;
use App\Models\Address;
use App\Models\Building;
use App\Models\BuildingDocument;
use App\Models\BuildingLevel;
use App\Models\BuildingPicture;
use App\Models\DropdownType;
use App\Models\Organization;
use App\Notifications\DatabaseOnlyNotification;
use App\Notifications\UserNotification;
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
        try {
            $buildings = null;
            $token = $request->attributes->get('token');

            if (!$token || !isset($token['organization_id'])) {
                return view('Heights.Owner.Buildings.index', compact('buildings'));
            }

            $organization_id = $token['organization_id'];
            $search = $request->input('search');

            $buildings = Building::with(['pictures', 'address'])
                ->where('organization_id', $organization_id)
                ->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('remarks', 'like', '%' . $search . '%')
                        ->orWhereHas('address', function ($q) use ($search) {
                            $q->where('city', 'like', '%' . $search . '%');
                        });
                })
                ->paginate(10);

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
        $token = $request->attributes->get('token');

        if (!$token || !isset($token['organization_id'])) {
            return redirect()->back()->withInput()->with('error', 'You cannot perform this action because they are not linked to any organization. Please switch to an organization account to proceed.');
        }

        $organization_id = $token['organization_id'];

        return $this->store($request, 'owner',$organization_id,'Under Processing',null);
    }

    private function store(Request $request, String $portal, $organization_id, $status, $remarks)
    {
        $user = $request->user() ?? abort(404, 'Page not found');

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

            $notificationImage = null;

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

                    $notificationImage = $imagePath;
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

            $link = "{$portal}/buildings/{$building->id}";
            $message = "{$building->name} has been added successfully.";

            if ($portal == 'admin') {

                dispatch(new OrganizationOwnerNotifications(
                    $organization_id,
                    $notificationImage,
                    'Building Added by Admin',
                    $message,
                    "owner/buildings/{$building->id}",

                    $user->id,
                    'Building Added',
                    $message,
                    $link,
                ));
                return redirect()->route('buildings.index')->with('success', 'Building created successfully.');

            } elseif ($portal == 'owner') {

                dispatch(new OrganizationOwnerNotifications(
                    $organization_id,
                    $notificationImage,
                    "Building Added by {$request->user()->name}",
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
    public function adminShow(Building $building)
    {
        return $this->show('admin', $building);
    }

    public function ownerShow(Building $building)
    {
        return $this->show('owner',$building);
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
    public function adminEdit(Building $building)
    {
        return $this->edit('admin', $building);
    }

    public function ownerEdit(Building $building)
    {
        return $this->edit('owner',$building);
    }

    private function edit(string $portal,Building $building)
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

        if (!$token || !isset($token['organization_id'])) {
            return redirect()->back()->withInput()->with('error', 'You cannot perform this action because they are not linked to any organization. Please switch to an organization account to proceed.');
        }

        $organization_id = $token['organization_id'];

        return $this->update($request, 'owner', $organization_id);
    }

    private function update(Request $request, String $portal, $organization_id)
    {
        $user = $request->user();

        $request->validate([
            'id' => 'required|exists:buildings,id',
            'name' => 'required|string|max:255|unique:buildings,name,'. $request->id . 'id',
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
                return $this->handleResponse($request,404,'error','Please refresh the page and try again.');
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

            $link = "{$portal}/buildings/{$building->id}";
            $message = "{$building->name} has been updated successfully.";
            $Image = $building->load(['pictures']);
            $notificationImage = $Image->pictures->first();

            if ($portal == 'admin') {

                dispatch(new OrganizationOwnerNotifications(
                    $organization_id,
                    $notificationImage->file_path,
                    'Building updated by Admin',
                    $message,
                    "owner/buildings/{$building->id}",

                    $user->id,
                    'Building updated',
                    $message,
                    $link,
                ));
                return redirect()->route('buildings.index')->with('success', 'Building updated successfully.');

            } elseif ($portal == 'owner') {

                dispatch(new OrganizationOwnerNotifications(
                    $organization_id,
                    $notificationImage->file_path,
                    "Building updated by {$request->user()->name}",
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


    // Approve/Reject/Submit for Approval Functions
    public function submitBuilding(Request $request){

        $user = $request->user() ?? abort(404, 'Page not found');
        $token = $request->attributes->get('token');
        $organization_id = $token['organization_id'] ?? null;

        $request->validate([
            'building_id' => 'required|integer|exists:buildings,id',
        ]);

        try {
            $building = Building::find($request->building_id);

            if(!$building)  abort(404, 'Page not found');

            $building->update([
                'status' => 'Under Review',
            ]);

            $message = "{$building->name} has been submitted successfully for approval to Admin";
            $Image = $building->load(['pictures']);
            $notificationImage = $Image->pictures->first();


            dispatch(new SendRoleNotification(
               1,
                $notificationImage->file_path,
               'New Building for approval',
               "{$building->name} is available for approval",
                "admin/buildings/{$building->id}",

                $user->id,
                "Building Submitted",
                $message,
                "owner/buildings/{$building->id}",
            ));

            dispatch(new OrganizationOwnerNotifications(
                $organization_id,
                $notificationImage->file_path,
                "Building Submitted by {$user->name}",
                $message,
                "owner/buildings/{$building->id}",

                $user->id,
            ));

            return redirect()->route('owner.buildings.index')->with('success', 'Building Submitted successfully.');
        }catch (\Exception $e) {
            Log::error('Error in submit building: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while updating the building.');
        }
    }

    public function approvalReminder(Request $request){

        $user = $request->user() ?? abort(404, 'Page not found');
        $token = $request->attributes->get('token');
        $organization_id = $token['organization_id'] ?? null;

        $request->validate([
            'building_id' => 'required|integer|exists:buildings,id',
        ]);

        try {
            $building = Building::find($request->building_id);

            if(!$building)  abort(404, 'Page not found');

            $Image = $building->load(['pictures']);
            $notificationImage = $Image->pictures->first();


            dispatch(new SendRoleNotification(
                1,
                $notificationImage->file_path,
                'Approval Reminder',
                "You have been reminded to review and approve the pending request of {$building->name}",
                "admin/buildings/{$building->id}",

                $user->id,
                "Reminder Sent",
                "Admin has been reminded successfully for {$building->name}",
                "owner/buildings/{$building->id}",
            ));

            dispatch(new OrganizationOwnerNotifications(
                $organization_id,
                $notificationImage->file_path,
                "Reminder Sent by {$user->name}",
                "Admin has been reminded successfully for {$building->name}",
                "owner/buildings/{$building->id}",
            ));

            return redirect()->route('owner.buildings.index')->with('success', 'Building Submitted successfully.');
        }catch (\Exception $e) {
            Log::error('Error in submit building: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while updating the building.');
        }
    }

    public function rejectBuilding(Request $request){

        $user = $request->user() ?? abort(404, 'Page not found');

        $request->validate([
            'building_id' => 'required|integer|exists:buildings,id',
            'remarks' => 'required|string',
        ]);

        try {
            $building = Building::find($request->building_id);
            if(!$building)  abort(404, 'Page not found');

            $building->update([
                'status' => 'Rejected',
                'remarks' => $request->remarks,
            ]);

            $Image = $building->load(['pictures']);
            $notificationImage = $Image->pictures->first();

            dispatch(new OrganizationOwnerNotifications(
                $building->organization_id,
                $notificationImage->file_path,
                "Building rejected",
                "{$building->name} has been rejected by admin with remarks {$request->remarks}",
                "owner/buildings/{$building->id}",

                $user->id,
                "Building rejected",
                "{$building->name} has been rejected successfully",
            ));

            return redirect()->route('buildings.index')->with('success', 'Building rejected successfully.');
        }catch (\Exception $e) {
            Log::error('Error in submit building: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while updating the building.');
        }
    }

    public function approveBuilding(Request $request){

        $user = $request->user() ?? abort(404, 'Page not found');

        $request->validate([
            'building_id' => 'required|integer|exists:buildings,id',
            'remarks' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $building = Building::find($request->building_id);
            if (!$building) abort(404, 'Page not found');

            $building->update([
                'status' => 'Approved',
                'remarks' => $request->remarks ?? null,
            ]);

            $building->levels()->update(['status' => 'Approved']);
            $building->units()->update(['status' => 'Approved']);

            DB::commit();

            $message = "{$building->name} has been approved successfully";
            $Image = $building->load(['pictures']);
            $notificationImage = $Image->pictures->first();

            dispatch(new OrganizationOwnerNotifications(
                $building->organization_id,
                $notificationImage->file_path,
                "Building approved",
                $message,
                "owner/buildings/{$building->id}",

                $user->id,
                "Building approved",
                $message,
                "admin/buildings/{$building->id}",
            ));

            return redirect()->route('buildings.index')->with('success', 'Building, levels, and units approved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in approving building: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while updating the building.');
        }
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
