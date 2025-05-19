<?php

namespace App\Http\Controllers\AppControllers;

use App\Http\Controllers\Controller;
use App\Models\Membership;
use App\Models\MembershipUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;


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
                ->where('status', 1)
                ->pluck('membership_id')
                ->toArray();

            $baseQuery = Membership::whereNotIn('id', $excludedMembershipIds)
                ->whereNotIn('status', ['Archived', 'Draft'])
                ->with([
                    'unit:id,unit_name',
                    'building:id,name'
                ])
                ->orderByDesc('updated_at');

            $featuredMemberships = (clone $baseQuery)
                ->where('mark_as_featured', 1)
                ->select('id', 'image', 'name', 'original_price', 'price', 'category')
                ->get();

            $memberships = (clone $baseQuery)
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

    public function show(Request $request, $id)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'error' => 'Unauthorized Action',
            ], 404);
        }

        try {
            $alreadyOwned = MembershipUser::where('user_id', $user->id)
                ->where('membership_id', $id)
                ->where('status', 1)
                ->exists();

            if ($alreadyOwned) {
                return response()->json([
                    'error' => 'You already own this membership.',
                ], 403);
            }

            $membership = Membership::where('id', $id)
                ->whereNotIn('status', ['Archived', 'Draft'])
                ->with([
                    'unit:id,unit_name',
                    'building:id,name'
                ])
                ->first();

            if (!$membership) {
                return response()->json([
                    'error' => 'Membership not found.',
                ], 404);
            }

            return response()->json([
                'membership' => $membership,
            ]);

        } catch (\Throwable $e) {
            Log::error('Error in Memberships show: ' . $e->getMessage());

            return response()->json([
                'error' => 'Something went wrong, please try again later.',
            ], 500);
        }
    }

    public function myMemberships(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'error' => 'Unauthorized Action',
            ], 404);
        }

        try {
            $membershipIds = MembershipUser::where('user_id', $user->id)
                ->where('status', 1)
                ->pluck('membership_id')
                ->toArray();

            $memberships = Membership::whereIn('id', $membershipIds)
                ->with([
                    'unit:id,unit_name',
                    'building:id,name'
                ])
                ->orderByDesc('updated_at')
                ->get();

            return response()->json([
                'my_memberships' => $memberships,
            ]);

        } catch (\Throwable $e) {
            Log::error('Error in myMemberships: ' . $e->getMessage());

            return response()->json([
                'error' => 'Something went wrong, please try again later.',
            ], 500);
        }
    }

    public function myMembershipShow(Request $request, $id)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'error' => 'Unauthorized Action',
            ], 404);
        }

        try {
            $membershipUser = MembershipUser::where('user_id', $user->id)
                ->where('membership_id', $id)
                ->where('status', 1)
                ->with([
                    'membership',
                    'membership.unit:id,unit_name',
                    'membership.building:id,name',
                    'subscription',
                ])
                ->first();

            if (!$membershipUser) {
                return response()->json([
                    'error' => 'Membership not found or not owned by user.',
                ], 404);
            }

            return response()->json([
                'details' => $membershipUser,
            ]);

        } catch (\Throwable $e) {
            Log::error('Error in show My Membership: ' . $e->getMessage());

            return response()->json([
                'error' => 'Something went wrong, please try again later.',
            ], 500);
        }
    }

    public function pastMemberships(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'error' => 'Unauthorized Action',
            ], 404);
        }

        try {
            $membershipIds = MembershipUser::where('user_id', $user->id)
                ->where('status', 0)
                ->pluck('membership_id')
                ->toArray();

            $pastMemberships = Membership::whereIn('id', $membershipIds)
                ->with([
                    'unit:id,unit_name',
                    'building:id,name',
                ])
                ->orderByDesc('updated_at')
                ->get();

            return response()->json([
                'past_memberships' => $pastMemberships,
            ]);

        } catch (\Throwable $e) {
            Log::error('Error in pastMemberships: ' . $e->getMessage());

            return response()->json([
                'error' => 'Something went wrong, please try again later.',
            ], 500);
        }
    }

    public function redeem(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'error' => 'Unauthorized Action',
            ], 404);
        }

        $request->validate([
            'id' => 'required|exists:membership_users,id',
        ]);

        try {
            $userMembership = MembershipUser::where('id', $request->id)
                ->where('user_id', $user->id)
                ->where('status', 1)
                ->with(['membership'])
                ->first();

            if (!$userMembership) {
                return response()->json([
                    'error' => 'Membership record not found.',
                ], 404);
            }

            if ($userMembership->used <= 0) {
                return response()->json([
                    'error' => 'No uses left for this membership.',
                ], 400);
            }

            $expirationTime = 5;
            $payload = JWTFactory::customClaims([
                'sub' => $user->id,
                'membership_user_id' => $userMembership->id,
                'exp' => Carbon::now()->addMinutes($expirationTime)->timestamp,
            ])->make();
            $token = JWTAuth::encode($payload)->get();

            $redeemBaseUrl = rtrim($userMembership->membership->url, '/');
            $finalUrl = $redeemBaseUrl . '/' . $token;

            return response()->json([
                'redeem_url' => $finalUrl,
                'message' => "Redeem URL generated. It expires in {$expirationTime} minutes.",
            ]);

        } catch (\Throwable $e) {
            Log::error('Error in redeem: ' . $e->getMessage());

            return response()->json([
                'error' => 'Something went wrong, please try again later.',
            ], 500);
        }
    }

    public function verifyToken(Request $request)
    {
        $request->validate([
            'token' => 'required',
        ]);

        try {
            $decoded = JWTAuth::setToken($request->token)->getPayload();
            $userId = $decoded->sub ?? null;
            $membershipId = $decoded->membership_user_id ?? null;

            if (!$userId || !$membershipId) {
                return response()->json([
                    'success' => false,
                    'error' => 'Invalid token payload.',
                ], 400);
            }

            $userMembership = MembershipUser::where('id', $membershipId)
                ->where('user_id', $userId)
                ->where('status', 1)
                ->with(['membership'])
                ->first();

            if (!$userMembership) {
                return response()->json([
                    'success' => false,
                    'error' => 'Membership record not found or inactive.',
                ], 404);
            }

            if ($userMembership->used <= 0) {
                return response()->json([
                    'success' => false,
                    'error' => 'No uses left for this membership.',
                ], 400);
            }

            $userMembership->decrement('used');

            return response()->json([
                'success' => true,
                'message' => 'Token verified and use recorded.',
                'membership' => $userMembership->membership,
            ]);

        } catch (TokenExpiredException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Token has expired.',
            ], 401);

        } catch (TokenInvalidException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Token is invalid.',
            ], 400);

        } catch (\Throwable $e) {
            Log::error('Error in verifyToken: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Something went wrong, please try again later.',
            ], 500);
        }
    }

}
