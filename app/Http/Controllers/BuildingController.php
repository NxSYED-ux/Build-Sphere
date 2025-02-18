<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Building;
use App\Models\BuildingDocument;
use App\Models\Organization;
use App\Models\DropdownType;   
use App\Models\BuildingPicture; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;  
use Illuminate\Support\Facades\File; 

use Illuminate\Http\Request;

class BuildingController extends Controller
{ 
    public function index(Request $request)
    {
        // Get the search query from the input
        $search = $request->input('search');

        // Query buildings and filter by search if a search term is provided
        $buildings = Building::with(['pictures', 'address', 'organization'])
            ->where('name', 'like', '%' . $search . '%')  // Search by name
            ->orWhere('remarks', 'like', '%' . $search . '%')  // Search by remarks
            ->orWhereHas('organization', function($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');  // Search by organization name
            })
            ->orWhereHas('address', function($query) use ($search) {
                $query->where('city', 'like', '%' . $search . '%');  // Search by city
            })
            ->paginate(10);  // Paginate the results

        // Return the view with the filtered buildings
        return view('Heights.Buildings.index', compact('buildings'));
    }
 
    public function create()
    { 
        $organizations = Organization::all();
        $dropdownData = DropdownType::with(['values.childs.childs'])->where('type_name', 'Country')->get(); // Country -> Province -> City
        $documentTypes = DropdownType::with(['values'])->where('type_name', 'Building-document-type')->first()->values->pluck('value_name', 'id');
        $buildingTypes = DropdownType::with(['values'])->where('type_name', 'Building-type')->first()->values()->where('status', 1)->get();
        return view('Heights.Buildings.create', compact('organizations', 'dropdownData', 'buildingTypes', 'documentTypes'));
    }
 
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
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
            'organization_id' => 'required|exists:organizations,id',
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
                'remarks' => $request->remarks,
                'status' => 4, 
                'construction_year' => $request->construction_year,  
                'organization_id' => $request->organization_id, 
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

            // Handle document uploads 
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

            return redirect()->route('buildings.index')->with('success', 'Building created successfully.'); 

        } catch (\Exception $e) {
            DB::rollBack();   
            return redirect()->back()->withInput()->with('error', 'An error occurred while creating the building.'); 
        }  
    }
 
    public function show(Building $building)
    { 
        $building->load([
            'address',
            'pictures',
            'organization.owner',
            'levels.units.pictures' 
        ]);

        // Extract necessary data 
        $owner = $building->organization->owner; 
        $levels = $building->levels;
        $units = $levels->flatMap->units;  

        return view('Heights.Buildings.show', compact('building', 'levels', 'units', 'owner'));
    } 
 
    public function edit(Building $building)
    { 
        $building->load(['address', 'organization','pictures', 'documents']);
        $organizations = Organization::all();
        $dropdownData = DropdownType::with(['values.childs.childs'])->where('type_name', 'Country')->get(); // Country -> Province -> City
        $documentTypes = DropdownType::with(['values']) ->where('type_name', 'Building-document-type')->first()->values->pluck('value_name', 'id');
        $buildingTypes = DropdownType::with(['values'])->where('type_name', 'Building-type')->first()->values;
        return view('Heights.Buildings.edit', compact('building', 'dropdownData', 'organizations', 'buildingTypes', 'documentTypes'));
    }
 
    public function update(Request $request, Building $building)
    {
        $request->validate([ 
            'name' => 'required|string|max:255',
            'building_type' => 'required|string|max:50',  
            'area' => 'nullable|numeric', 
            'status' => 'required|string',
            'construction_year' => 'nullable|integer|min:1800|max:' . date('Y'), 
            'location' => 'nullable|string|max:255',  
            'country' => 'nullable|string|max:50',  
            'province' => 'nullable|string|max:50',  
            'city' => 'nullable|string|max:50',  
            'postal_code' => 'nullable|string|max:50',  
            'organization_id' => 'required|exists:organizations,id',
            'building_pictures.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

            'documents.*.type' => 'nullable|string|distinct',
            'documents.*.issue_date' => 'nullable|date',
            'documents.*.expiry_date' => 'nullable|date|after:documents.*.issue_date',
            'documents.*.files' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5048',
        ]);


        DB::beginTransaction();  

        try { 
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
                'organization_id' => $request->organization_id, 
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

            // Handle document uploads 
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

            return redirect()->route('buildings.index')->with('success', 'Building updated successfully.'); 

        } catch (\Exception $e) {
            DB::rollBack();   
            return redirect()->back()->withInput()->with('error', 'An error occurred while updating the building.'); 
        }  
    } 

    public function destroyImage(string $id)
    {
        $image = BuildingPicture::findOrFail($id);

        if ($image) {
            $oldImagePath = public_path($image->file_path); // Corrected variable name
            if (File::exists($oldImagePath)) {
                File::delete($oldImagePath);
            }
            
            // Delete the image record from the database
            $image->delete();
        }

        return response()->json(['success' => true]);
    } 

    public function removeDocument(Request $request, $fileId)
    {
        $document = BuildingDocument::find($fileId);

        if ($document) {
            // Delete the file from the storage 
            $oldImagePath = public_path($document->file_path); // Corrected variable name
            if (File::exists($oldImagePath)) {
                File::delete($oldImagePath);
            }

            // Delete the record from the database
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
    
        if ($file) {
            // Validate request data
            $request->validate([
                'document_type' => 'required|string',
                'issue_date' => 'nullable|date',
                'expiry_date' => 'nullable|date',
                'file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5048'
            ]);
    
            // Update document fields
            $file->document_type = $request->document_type;
            $file->issue_date = $request->issue_date;
            $file->expiry_date = $request->expiry_date;
    
            if ($request->hasFile('file')) {
                // Delete the old file
                $oldImagePath = public_path($file->file_path);
                if (File::exists($oldImagePath)) {
                    File::delete($oldImagePath);
                }
    
                // Store the new file
                $newFile = $request->file('file');
                $fileName = time() . '_' . $newFile->getClientOriginalName();
                $filePath = 'uploads/buildings/document/' . $request->document_type . '/' . $fileName;
                $newFile->move(public_path('uploads/buildings/document/' . $request->document_type), $fileName);
                $file->file_path = $filePath;
                $file->file_name = $fileName;
            }
    
            $file->save();

            return redirect()->back()->with('success', 'Document updated successfully!');
        } else {
            return redirect()->back()->with('error', 'Document not found.')->setStatusCode(404);
        }
    }
}
