<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Friendship;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class FriendshipApiController extends Controller
{
    /**
     * Get current user's friends list
     */
    public function index(): JsonResponse
    {
        try {
            $user = Auth::user();

            // Get accepted friendships where user is either requester or addressee
            $friendships = Friendship::where(function ($query) use ($user) {
                $query->where('requester_id', $user->id)
                    ->orWhere('addressee_id', $user->id);
            })
                ->where('status', 'accepted')
                ->with(['requester', 'addressee'])
                ->get();

            $friends = $friendships->map(function ($friendship) use ($user) {
                $friend = $friendship->requester_id === $user->id
                    ? $friendship->addressee
                    : $friendship->requester;

                return [
                    'id' => $friend->id,
                    'name' => $friend->name,
                    'username' => $friend->username,
                    'avatar' => $friend->avatar,
                    'last_login_at' => $friend->last_login_at?->diffForHumans(),
                    'games_won' => $friend->games_won,
                    'games_lost' => $friend->games_lost,
                    'games_drawn' => $friend->games_drawn,
                    'win_rate' => $friend->getWinRateAttribute(),
                    'friendship_created_at' => $friendship->created_at->diffForHumans()
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $friends,
                'count' => $friends->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load friends list',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get pending friend requests (received by current user)
     */
    public function getPendingRequests(): JsonResponse
    {
        try {
            $user = Auth::user();

            $pendingRequests = Friendship::where('addressee_id', $user->id)
                ->where('status', 'pending')
                ->with('requester')
                ->orderBy('created_at', 'desc')
                ->get();

            $requests = $pendingRequests->map(function ($friendship) {
                return [
                    'id' => $friendship->id,
                    'requester' => [
                        'id' => $friendship->requester->id,
                        'name' => $friendship->requester->name,
                        'username' => $friendship->requester->username,
                        'avatar' => $friendship->requester->avatar,
                        'games_won' => $friendship->requester->games_won,
                        'win_rate' => $friendship->requester->getWinRateAttribute()
                    ],
                    'sent_at' => $friendship->created_at->diffForHumans(),
                    'status' => $friendship->status
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $requests,
                'count' => $requests->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load pending requests',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get sent friend requests (sent by current user)
     */
    public function getSentRequests(): JsonResponse
    {
        try {
            $user = Auth::user();

            $sentRequests = Friendship::where('requester_id', $user->id)
                ->where('status', 'pending')
                ->with('addressee')
                ->orderBy('created_at', 'desc')
                ->get();

            $requests = $sentRequests->map(function ($friendship) {
                return [
                    'id' => $friendship->id,
                    'addressee' => [
                        'id' => $friendship->addressee->id,
                        'name' => $friendship->addressee->name,
                        'username' => $friendship->addressee->username,
                        'avatar' => $friendship->addressee->avatar,
                        'games_won' => $friendship->addressee->games_won,
                        'win_rate' => $friendship->addressee->getWinRateAttribute()
                    ],
                    'sent_at' => $friendship->created_at->diffForHumans(),
                    'status' => $friendship->status
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $requests,
                'count' => $requests->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load sent requests',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Search for users to add as friends
     */
    public function searchUsers(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'query' => 'required|string|min:2|max:50'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid search query',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();
            $searchQuery = $request->input('query');

            // Search users by username or email, excluding current user
            $users = User::where('id', '!=', $user->id)
                ->where(function ($query) use ($searchQuery) {
                    $query->where('username', 'like', "%{$searchQuery}%")
                        ->orWhere('email', 'like', "%{$searchQuery}%")
                        ->orWhere('name', 'like', "%{$searchQuery}%");
                })
                ->limit(10)
                ->get();

            $results = $users->map(function ($foundUser) use ($user) {
                $friendshipStatus = $this->getFriendshipStatus($user, $foundUser);

                return [
                    'id' => $foundUser->id,
                    'name' => $foundUser->name,
                    'username' => $foundUser->username,
                    'avatar' => $foundUser->avatar,
                    'games_won' => $foundUser->games_won,
                    'win_rate' => $foundUser->getWinRateAttribute(),
                    'friendship_status' => $friendshipStatus,
                    'can_send_request' => $friendshipStatus === 'none'
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $results,
                'count' => $results->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Search failed',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Send a friend request
     */
    public function sendRequest(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|integer|exists:users,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid user ID',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();
            $targetUserId = $request->input('user_id');

            // Check if trying to add themselves
            if ($user->id === $targetUserId) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot add yourself as a friend'
                ], 422);
            }

            $targetUser = User::findOrFail($targetUserId);

            // Check if friendship already exists
            $existingFriendship = Friendship::where(function ($query) use ($user, $targetUserId) {
                $query->where('requester_id', $user->id)
                    ->where('addressee_id', $targetUserId);
            })->orWhere(function ($query) use ($user, $targetUserId) {
                $query->where('requester_id', $targetUserId)
                    ->where('addressee_id', $user->id);
            })->first();

            if ($existingFriendship) {
                $message = match($existingFriendship->status) {
                    'accepted' => 'You are already friends with this user',
                    'pending' => $existingFriendship->requester_id === $user->id
                        ? 'Friend request already sent'
                        : 'This user has already sent you a friend request',
                    'declined' => 'Friend request was previously declined',
                    default => 'Friendship already exists'
                };

                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 422);
            }

            // Create new friend request
            $friendship = Friendship::create([
                'requester_id' => $user->id,
                'addressee_id' => $targetUserId,
                'status' => 'pending'
            ]);

            return response()->json([
                'success' => true,
                'message' => "Friend request sent to {$targetUser->username}",
                'data' => [
                    'friendship_id' => $friendship->id,
                    'target_user' => [
                        'id' => $targetUser->id,
                        'name' => $targetUser->name,
                        'username' => $targetUser->username,
                        'avatar' => $targetUser->avatar
                    ]
                ]
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send friend request',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Accept a friend request
     */
    public function acceptRequest(Request $request, $friendshipId): JsonResponse
    {
        try {
            $user = Auth::user();

            $friendship = Friendship::where('id', $friendshipId)
                ->where('addressee_id', $user->id)
                ->where('status', 'pending')
                ->with('requester')
                ->firstOrFail();

            $friendship->update(['status' => 'accepted']);

            return response()->json([
                'success' => true,
                'message' => "You are now friends with {$friendship->requester->username}",
                'data' => [
                    'friendship_id' => $friendship->id,
                    'friend' => [
                        'id' => $friendship->requester->id,
                        'name' => $friendship->requester->name,
                        'username' => $friendship->requester->username,
                        'avatar' => $friendship->requester->avatar,
                        'games_won' => $friendship->requester->games_won,
                        'win_rate' => $friendship->requester->getWinRateAttribute()
                    ]
                ]
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Friend request not found or already processed'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to accept friend request',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Decline a friend request
     */
    public function declineRequest(Request $request, $friendshipId): JsonResponse
    {
        try {
            $user = Auth::user();

            $friendship = Friendship::where('id', $friendshipId)
                ->where('addressee_id', $user->id)
                ->where('status', 'pending')
                ->with('requester')
                ->firstOrFail();

            $friendship->update(['status' => 'declined']);

            return response()->json([
                'success' => true,
                'message' => "Friend request from {$friendship->requester->username} declined",
                'data' => [
                    'friendship_id' => $friendship->id
                ]
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Friend request not found or already processed'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to decline friend request',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Cancel a sent friend request
     */
    public function cancelRequest(Request $request, $friendshipId): JsonResponse
    {
        try {
            $user = Auth::user();

            $friendship = Friendship::where('id', $friendshipId)
                ->where('requester_id', $user->id)
                ->where('status', 'pending')
                ->with('addressee')
                ->firstOrFail();

            $friendship->delete();

            return response()->json([
                'success' => true,
                'message' => "Friend request to {$friendship->addressee->username} cancelled",
                'data' => [
                    'friendship_id' => $friendship->id
                ]
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Friend request not found or already processed'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel friend request',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Remove a friend (unfriend)
     */
    public function removeFriend(Request $request, $friendId): JsonResponse
    {
        try {
            $user = Auth::user();

            $friendship = Friendship::where(function ($query) use ($user, $friendId) {
                $query->where('requester_id', $user->id)
                    ->where('addressee_id', $friendId);
            })->orWhere(function ($query) use ($user, $friendId) {
                $query->where('requester_id', $friendId)
                    ->where('addressee_id', $user->id);
            })
                ->where('status', 'accepted')
                ->with(['requester', 'addressee'])
                ->firstOrFail();

            $friend = $friendship->requester_id === $user->id
                ? $friendship->addressee
                : $friendship->requester;

            $friendship->delete();

            return response()->json([
                'success' => true,
                'message' => "You are no longer friends with {$friend->username}",
                'data' => [
                    'removed_friend_id' => $friend->id
                ]
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Friendship not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove friend',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get friendship statistics
     */
    public function getStats(): JsonResponse
    {
        try {
            $user = Auth::user();

            $totalFriends = Friendship::where(function ($query) use ($user) {
                $query->where('requester_id', $user->id)
                    ->orWhere('addressee_id', $user->id);
            })->where('status', 'accepted')->count();

            $pendingReceived = Friendship::where('addressee_id', $user->id)
                ->where('status', 'pending')->count();

            $pendingSent = Friendship::where('requester_id', $user->id)
                ->where('status', 'pending')->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_friends' => $totalFriends,
                    'pending_requests_received' => $pendingReceived,
                    'pending_requests_sent' => $pendingSent,
                    'total_pending' => $pendingReceived + $pendingSent
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load friendship statistics',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Helper method to determine friendship status between two users
     */
    private function getFriendshipStatus(User $currentUser, User $targetUser): string
    {
        $friendship = Friendship::where(function ($query) use ($currentUser, $targetUser) {
            $query->where('requester_id', $currentUser->id)
                ->where('addressee_id', $targetUser->id);
        })->orWhere(function ($query) use ($currentUser, $targetUser) {
            $query->where('requester_id', $targetUser->id)
                ->where('addressee_id', $currentUser->id);
        })->first();

        if (!$friendship) {
            return 'none';
        }

        if ($friendship->status === 'accepted') {
            return 'friends';
        }

        if ($friendship->status === 'pending') {
            return $friendship->requester_id === $currentUser->id
                ? 'request_sent'
                : 'request_received';
        }

        return $friendship->status; // declined, blocked, etc.
    }
}
