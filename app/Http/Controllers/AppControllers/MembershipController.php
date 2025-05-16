<?php

namespace App\Http\Controllers\AppControllers;

use App\Http\Controllers\Controller;
use App\Models\Membership;
use App\Models\MembershipUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class MembershipController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'error' => 'Unauthorized Action',
            ], 404);
        }

        try {
            $excludedMembershipIds = MembershipUser::where('user_id', $user->id)
                ->pluck('membership_id')
                ->toArray();

            $baseQuery = Membership::whereNotIn('id', $excludedMembershipIds)
                ->where('status', '!=', 'Archived')
                ->with([
                    'unit:id,unit_name',
                    'building:id,name'
                ])
                ->orderByDesc('updated_at');

            $featuredMemberships = (clone $baseQuery)
                ->where('mark_as_featured', 1)
                ->get();

            $memberships = (clone $baseQuery)
                ->where('mark_as_featured', 0)
                ->get();

            return response()->json([
                'memberships' => $memberships,
                'featured_memberships' => $featuredMemberships,
            ]);

        } catch (\Throwable $e) {
            Log::error('Error in Memberships index: ' . $e->getMessage());

            return response()->json([
                'error' => 'Something went wrong, please try again later.',
            ], 500);
        }
    }

    


}
