<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\BuildingUnit;
use App\Models\ManagerBuilding;
use App\Models\Membership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;


class MembershipController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user() ?? abort(404, 'Page not found');

        try {
            $memberships = collect();
            $buildings = collect();
            $units = collect();
            $types = ['Restaurant', 'Gym', 'Other'];
            $statuses = ['Draft', 'Published', 'Non Renewable', 'Archived'];

            $token = $request->attributes->get('token');

            if (empty($token['organization_id']) || empty($token['role_name'])) {
                return view('Heights.Owner.Memberships.index', compact('memberships', 'buildings', 'units', 'types', 'statuses'));
            }

            $organization_id = $token['organization_id'];
            $role_name = $token['role_name'];

            $membershipQuery = Membership::where('organization_id', $organization_id)
                ->with([
                    'unit:id,unit_name',
                    'building:id,name'
                ]);

            $buildingsQuery = Building::where('organization_id', $organization_id)
                ->whereIn('status', ['Approved', 'For Re-Approval'])
                ->where('isFreeze', 0)
                ->select('id', 'name');

            $unitsQuery = BuildingUnit::where('organization_id', $organization_id)
                ->where('status', 'Approved')
                ->where('availability_status', 'Available')
                ->where('sale_or_rent', 'Not Available')
                ->whereNotIn('unit_type', ['Room', 'Shop', 'Apartment'])
                ->select('id', 'unit_name');

            if ($role_name === 'Manager') {
                $managerBuildingIds = ManagerBuilding::where('user_id', $user->id)->pluck('building_id');

                if ($managerBuildingIds->isEmpty()) {
                    $memberships = collect();
                    return view('Heights.Owner.Memberships.index', compact('memberships', 'buildings', 'units', 'types', 'statuses'));
                }

                $membershipQuery->whereIn('building_id', $managerBuildingIds);
                $buildingsQuery->whereIn('id', $managerBuildingIds);
                $unitsQuery->whereIn('building_id', $managerBuildingIds);
            }

            if ($request->filled('building_id')) {
                $membershipQuery->where('building_id', $request->input('building_id'));
            }

            if ($request->filled('unit_id')) {
                $membershipQuery->where('unit_id', $request->input('unit_id'));
            }

            if ($request->filled('type') && in_array($request->input('type'), $types)) {
                $membershipQuery->where('category', $request->input('type'));
            }

            if ($request->filled('status') && in_array($request->input('status'), $statuses)) {
                $membershipQuery->where('status', $request->input('status'));
            }

            if ($request->filled('min_price')) {
                $membershipQuery->where('price', '>=', $request->input('min_price'));
            }

            if ($request->filled('max_price')) {
                $membershipQuery->where('price', '<=', $request->input('max_price'));
            }

            if ($request->has('featured') && in_array($request->featured, ['0', '1'])) {
                $membershipQuery->where('mark_as_featured', $request->featured);
            }

            if ($request->filled('search')) {
                $searchTerm = $request->input('search');
                $membershipQuery->where('name', 'like', '%' . $searchTerm . '%');
            }

            $memberships = $membershipQuery->paginate(12);
            $buildings = $buildingsQuery->get();
            $units = $unitsQuery->get();

            return view('Heights.Owner.Memberships.index', compact('memberships', 'buildings', 'units', 'types', 'statuses'));

        } catch (\Throwable $e) {
            Log::error('Error in Memberships index: ' . $e->getMessage());
            return back()->with('error', 'Something went wrong! Please try again.');
        }
    }


    public function create(Request $request)
    {
        $user = $request->user() ?? abort(404, 'Page not found');

        try {
            $buildings = collect();
            $units = collect();
            $types = ['Restaurant', 'Gym', 'Other'];
            $statuses = ['Draft', 'Published', 'Non Renewable'];
            $currency = ['PKR'];

            $token = $request->attributes->get('token');

            if (empty($token['organization_id']) || empty($token['role_name'])) {
                return view('Heights.Owner.Memberships.create', compact('buildings', 'units', 'types', 'statuses', 'currency'));
            }

            $organization_id = $token['organization_id'];
            $role_name = $token['role_name'];

            $buildingsQuery = Building::where('organization_id', $organization_id)
                ->whereIn('status', ['Approved', 'For Re-Approval'])
                ->where('isFreeze', 0)
                ->select('id', 'name');

            $unitsQuery = BuildingUnit::where('organization_id', $organization_id)
                ->where('status', 'Approved')
                ->where('availability_status', 'Available')
                ->where('sale_or_rent', 'Not Available')
                ->whereNotIn('unit_type', ['Room', 'Shop', 'Apartment'])
                ->select('id', 'unit_name');

            if ($role_name === 'Manager') {
                $managerBuildingIds = ManagerBuilding::where('user_id', $user->id)->pluck('building_id');

                if ($managerBuildingIds->isEmpty()) {
                    return view('Heights.Owner.Memberships.create', compact('buildings', 'units', 'types', 'statuses', 'currency'));
                }

                $buildingsQuery->whereIn('id', $managerBuildingIds);
                $unitsQuery->whereIn('building_id', $managerBuildingIds);
            }

            $buildings = $buildingsQuery->get();
            $units = $unitsQuery->get();

            return view('Heights.Owner.Memberships.create', compact('buildings', 'units', 'types', 'statuses', 'currency'));

        } catch (\Throwable $e) {
            Log::error('Error in Memberships create: ' . $e->getMessage());
            return back()->with('error', 'Something went wrong! Please try again.');
        }
    }


    public function store(Request $request)
    {
        $user = request()->user() ?? abort(404, 'Unauthorized Action');

        $request->validate([
            'unit_id' => 'required|exists:buildingunits,id',
            'building_id' => 'required|exists:buildings,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'name' => ['required', 'string', 'max:100',
                Rule::unique('memberships')->where(function ($query) use ($request) {
                    return $query->where('unit_id', $request->unit_id);
                }),
            ],
            'url' => 'required|url|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:Gym,Restaurant,Other',
            'duration_months' => 'required|integer|min:1',
            'scans_per_day' => 'required|integer|min:1',
            'currency' => 'nullable|string|max:10',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'status' => 'in:Draft,Published,Non Renewable,Archived',
        ]);

        try {
            $token = $request->attributes->get('token');

            if (empty($token['organization_id']) || empty($token['role_name'])) {
                return redirect()->back()->withInput()->with('error', 'Only organization related personals can perform this action.');
            }

            $organization_id = $token['organization_id'];
            $role_name = $token['role_name'];

            if ($role_name === 'Manager' && !ManagerBuilding::where('building_id', $request->building_id)
                    ->where('user_id', $user->id)
                    ->exists()) {
                return redirect()->back()->withInput()->with('error', 'You do not have access to add memberships for the selected building.');
            }

            $unit = BuildingUnit::where('id', $request->unit_id)
                ->where('organization_id', $organization_id)->first();

            if (!$unit || $unit->building_id !== (int)$request->building_id) {
                return redirect()->back()->withInput()->with('error', 'Selected unit does not belong to the selected building.');
            }

            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $this->handleImageUpload($request);
            }

            Membership::create([
                'organization_id' => $organization_id,
                'unit_id' => $request->unit_id,
                'building_id' => $request->building_id,
                'image' => $imagePath ?? 'uploads/memberships/images/defaultImage.jpeg',
                'name' => $request->name,
                'url' => $request->url,
                'description' => $request->description,
                'category' => $request->category,
                'duration_months' => $request->duration_months,
                'scans_per_day' => $request->scans_per_day,
                'currency' => $request->currency,
                'price' => $request->price,
                'original_price' => $request->original_price,
                'status' => $request->status,
            ]);



            return redirect()->route('owner.memberships.index')->with('success', 'Membership created successfully.');

        } catch (\Throwable $e) {
            Log::error('Error in Memberships store: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Something went wrong! Please try again.');
        }
    }


    public function edit(Request $request, $id){

    }

    public function update(Request $request, $id){

    }

    public function show(Request $request, $id){

    }

    // Helper Functions
    private function handleImageUpload(Request $request): string
    {
        $image = $request->file('image');
        $imageName = time() . '_' . $image->getClientOriginalName();
        $imagePath = 'uploads/memberships/images/' . $imageName;
        $image->move(public_path('uploads/memberships/images'), $imageName);
        return $imagePath;
    }

}
