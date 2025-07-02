<?php

namespace App\Http\Controllers\AppControllers;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\UserPropertyInteraction;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FavouritesController extends Controller
{
    public function showFavourites(Request $request)
    {
        try {
            $user = $request->user() ?? null;
            if (!$user) {
                return response()->json(['error' => 'User not authenticated.'], 401);
            }

            $favorites = Favorite::where('user_id', $user->id)
                ->whereHas('unit', function ($query) {
                    $query->where([
                        ['sale_or_rent', '!=', 'Not Available'],
                        ['availability_status', '=', 'Available'],
                        ['status', '=', 'Approved']
                    ])
                        ->whereHas('level', function ($levelQuery) {
                            $levelQuery->whereHas('building', function ($buildingQuery) {
                                $buildingQuery->whereIn('status', ['Approved', 'For Re-Approval'])
                                    ->where('isFreeze', 0)
                                    ->whereHas('address');
                            });
                        });
                })
                ->with([
                    'unit' => function ($query) {
                        $query->select('id', 'unit_name', 'unit_type', 'level_id', 'price', 'sale_or_rent')
                            ->with([
                                'level' => function ($levelQuery) {
                                    $levelQuery->select('id', 'level_name', 'building_id')
                                        ->with([
                                            'building' => function ($buildingQuery) {
                                                $buildingQuery->select('id', 'name', 'address_id')
                                                    ->with([
                                                        'address:id,location,city,province,country'
                                                    ]);
                                            }
                                        ]);
                                },
                                'pictures:unit_id,file_path'
                            ]);
                    }
                ])
                ->select('unit_id')
                ->orderBy('created_at', 'DESC')
                ->get();

            return response()->json(['favorites' => $favorites], 200);
        } catch (\Exception $e) {
            Log::error("Error in showFavourites: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function insertFavorite(Request $request)
    {
        $user = $request->user() ?? null;

        if (!$user) {
            return response()->json(['error' => 'User not authenticated.'], 401);
        }

        $request->validate([
            'unit_id' => 'required|integer|exists:buildingunits,id',
        ]);

        try {
            $existingFavorite = Favorite::where([
                'user_id' => $user->id,
                'unit_id' => $request->unit_id,
            ])->first();

            if ($existingFavorite) {
                return response()->json(['error' => 'This unit is already in your favorites.'], 409);
            }

            Favorite::create([
                'user_id' => $user->id,
                'unit_id' => $request->unit_id,
            ]);

            UserPropertyInteraction::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'unit_id' => $request->unit_id,
                    'interaction_type' => 'favourite'
                ],
                [
                    'timestamp' => now()
                ]
            );

            return response()->json(['message' => 'Favorite added successfully.'], 201);
        } catch (\Exception $e) {
            Log::error("Error in insertFavorite: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function deleteFavorite(Request $request, $unit_id)
    {
        $user = $request->user() ?? null;
        if (!$user) {
            return response()->json(['error' => 'User not authenticated.'], 401);
        }

        if (!is_numeric($unit_id)) {
            return response()->json(['error' => 'Invalid unit ID.'], 400);
        }

        try {
            $existingFavorite = Favorite::where([
                'user_id' => $user->id,
                'unit_id' => $unit_id
            ])->first();

            if (!$existingFavorite) {
                return response()->json(['error' => 'Favorite not found.'], 404);
            }

            $existingFavorite->delete();

            UserPropertyInteraction::where([
                'user_id' => $user->id,
                'unit_id' => $unit_id,
                'interaction_type' => 'favourite',
            ])->delete();

            return response()->json(['message' => 'Favorite deleted successfully.']);
        } catch (\Exception $e) {
            Log::error('Error deleting favorite: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function favouritesList(Request $request)
    {
        $user = $request->user() ?? null;
        if (!$user) {
            return response()->json(['error' => 'User not authenticated.'], 401);
        }

        try {
            $favorites = Favorite::where('user_id', $user->id)
                ->select('unit_id')
                ->get();

            return response()->json([
                'favorites_list' => $favorites,
            ]);
        } catch (\Exception $e) {
            Log::error("Error in favouritesList: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
