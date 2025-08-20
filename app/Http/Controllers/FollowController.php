<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    /**
     * Suivre un utilisateur
     * Route: POST /api/users/{userId}/follow
     */
    public function followUser(Request $request, $userId)
    {
      
        $request->validate([
            'follower_id' => 'required|integer|exists:users,id',
        ]);

        $followerId = $request->follower_id;

        // Vérifier que l'utilisateur à suivre existe
        $userToFollow = User::find($userId);
        if (!$userToFollow) {
            return response()->json([
                'success' => false,
                'error' => 'User not found'
            ], 404);
        }

        // Empêcher qu'un utilisateur se suive lui-même
        if ($followerId == $userId) {
            return response()->json([
                'success' => false,
                'error' => 'You cannot follow yourself'
            ], 400);
        }

        // Vérifier si la relation existe déjà
        $existingFollow = Follow::where([
            'follower_id' => $followerId,
            'following_id' => $userId,
        ])->first();

        if ($existingFollow) {
            return response()->json([
                'success' => false,
                'message' => 'Already following this user'
            ], 409);
        }

        // Créer la relation de follow
        $follow = Follow::create([
            'follower_id' => $followerId,
            'following_id' => $userId,
        ]);

        // Compter les nouveaux totaux
        $followersCount = Follow::where('following_id', $userId)->count();
        $followingCount = Follow::where('follower_id', $followerId)->count();

        return response()->json([
            'success' => true,
            'message' => 'User followed successfully',
            'data' => [
                'follow_id' => $follow->id,
                'is_following' => true,
                'user_followers_count' => $followersCount,
                'your_following_count' => $followingCount,
                'followed_at' => $follow->created_at,
            ]
        ], 201);
    }

    /**
     * Ne plus suivre un utilisateur
     * Route: DELETE /api/users/{userId}/follow
     */
    public function unfollowUser(Request $request, $userId)
    {
        $request->validate([
            'follower_id' => 'required|integer|exists:users,id',
        ]);

        $followerId = $request->follower_id;

        $follow = Follow::where([
            'follower_id' => $followerId,
            'following_id' => $userId,
        ])->first();

        if (!$follow) {
            return response()->json([
                'success' => false,
                'error' => 'Follow relationship not found'
            ], 404);
        }

        $follow->delete();

        // Compter les nouveaux totaux
        $followersCount = Follow::where('following_id', $userId)->count();
        $followingCount = Follow::where('follower_id', $followerId)->count();

        return response()->json([
            'success' => true,
            'message' => 'User unfollowed successfully',
            'data' => [
                'is_following' => false,
                'user_followers_count' => $followersCount,
                'your_following_count' => $followingCount,
            ]
        ]);
    }

    /**
     * Obtenir la liste des followers d'un utilisateur
     * Route: GET /api/users/{userId}/followers
     */
    public function getUserFollowers($userId, Request $request)
    {
        $user = User::find($userId);
        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'User not found'
            ], 404);
        }

        $page = $request->get('page', 1);
        $limit = $request->get('limit', 20);

        $followers = Follow::with('follower:id,first_name,last_name,profile_image,email')
            ->where('following_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($limit, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'data' => [
                'followers' => $followers->map(function ($follow) {
                    return [
                        'id' => $follow->follower->id,
                        'name' => trim($follow->follower->first_name . ' ' . $follow->follower->last_name),
                        'email' => $follow->follower->email,
                        'avatar' => $follow->follower->profile_image,
                        'followed_at' => $follow->created_at,
                    ];
                }),
                'total_followers' => $followers->total(),
                'pagination' => [
                    'current_page' => $followers->currentPage(),
                    'last_page' => $followers->lastPage(),
                    'per_page' => $followers->perPage(),
                    'total' => $followers->total(),
                ]
            ]
        ]);
    }

    /**
     * Obtenir la liste des utilisateurs suivis par un utilisateur
     * Route: GET /api/users/{userId}/following
     */
    public function getUserFollowing($userId, Request $request)
    {
        $user = User::find($userId);
        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'User not found'
            ], 404);
        }

        $page = $request->get('page', 1);
        $limit = $request->get('limit', 20);

        $following = Follow::with('following:id,first_name,last_name,profile_image,email')
            ->where('follower_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($limit, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'data' => [
                'following' => $following->map(function ($follow) {
                    return [
                        'id' => $follow->following->id,
                        'name' => trim($follow->following->first_name . ' ' . $follow->following->last_name),
                        'email' => $follow->following->email,
                        'avatar' => $follow->following->profile_image,
                        'followed_at' => $follow->created_at,
                    ];
                }),
                'total_following' => $following->total(),
                'pagination' => [
                    'current_page' => $following->currentPage(),
                    'last_page' => $following->lastPage(),
                    'per_page' => $following->perPage(),
                    'total' => $following->total(),
                ]
            ]
        ]);
    }

    /**
     * Vérifier si un utilisateur suit un autre utilisateur
     * Route: GET /api/users/{userId}/follow/check
     */
    public function checkFollowStatus(Request $request, $userId)
    {
        $request->validate([
            'follower_id' => 'required|integer|exists:users,id',
        ]);

        $followerId = $request->follower_id;

        $isFollowing = Follow::where([
            'follower_id' => $followerId,
            'following_id' => $userId,
        ])->exists();

        return response()->json([
            'success' => true,
            'data' => [
                'is_following' => $isFollowing,
                'follower_id' => $followerId,
                'following_id' => $userId,
            ]
        ]);
    }

    /**
     * Obtenir les statistiques de follow d'un utilisateur
     * Route: GET /api/users/{userId}/follow/stats
     */
    public function getUserFollowStats($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'User not found'
            ], 404);
        }

        $followersCount = Follow::where('following_id', $userId)->count();
        $followingCount = Follow::where('follower_id', $userId)->count();

        // Followers récents (dernières 24h)
        $recentFollowersCount = Follow::where('following_id', $userId)
            ->where('created_at', '>=', now()->subDay())
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'user_id' => $userId,
                'followers_count' => $followersCount,
                'following_count' => $followingCount,
                'recent_followers_24h' => $recentFollowersCount,
                'follow_ratio' => $followersCount > 0 ? round($followingCount / $followersCount, 2) : 0,
            ]
        ]);
    }

    /**
     * Obtenir les followers mutuels entre deux utilisateurs
     * Route: GET /api/users/{userId}/mutual-followers
     */
    public function getMutualFollowers(Request $request, $userId)
    {
        $request->validate([
            'other_user_id' => 'required|integer|exists:users,id',
        ]);

        $otherUserId = $request->other_user_id;

        // Followers de l'utilisateur 1
        $user1Followers = Follow::where('following_id', $userId)->pluck('follower_id');

        // Followers de l'utilisateur 2
        $user2Followers = Follow::where('following_id', $otherUserId)->pluck('follower_id');

        // Intersection (followers mutuels)
        $mutualFollowerIds = $user1Followers->intersect($user2Followers);

        $mutualFollowers = User::whereIn('id', $mutualFollowerIds)
            ->select('id', 'first_name', 'last_name', 'profile_image', 'email')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => trim($user->first_name . ' ' . $user->last_name),
                    'email' => $user->email,
                    'avatar' => $user->profile_image,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'mutual_followers' => $mutualFollowers,
                'count' => $mutualFollowers->count(),
                'user1_id' => $userId,
                'user2_id' => $otherUserId,
            ]
        ]);
    }
}
