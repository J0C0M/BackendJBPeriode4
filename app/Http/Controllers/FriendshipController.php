<?php

namespace App\Http\Controllers;

use App\Models\Friendship;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FriendshipController extends Controller
{
    /**
     * Display the user's friends list and pending requests
     */
    public function index()
    {
        $user = Auth::user();
        $friends = $user->friends()->get();
        $sentRequests = $user->sentFriendRequests()->where('status', 'pending')->with('addressee')->get();
        $receivedRequests = $user->receivedFriendRequests()->where('status', 'pending')->with('requester')->get();
        $pendingRequests = $sentRequests->count() + $receivedRequests->count();
        return view('friends.index', compact('friends', 'sentRequests', 'receivedRequests', 'pendingRequests'));
    }

    /**
     * Show form to search and add friends
     */
    public function create()
    {
        return view('friends.create');
    }

    /**
     * Search for users to add as friends
     */
    public function search(Request $request)
    {
        $request->validate([
            'search' => 'required|string|min:2'
        ]);

        $user = Auth::user();
        $search = $request->input('search');

        // Get users matching the search, excluding current user
        $users = User::where('id', '!=', $user->id)
            ->where(function($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('username', 'LIKE', "%{$search}%");
            })
            ->limit(10)
            ->get();

        // Get friendship status for each user
        $usersWithStatus = $users->map(function($foundUser) use ($user) {
            $friendship = \App\Models\Friendship::where(function ($query) use ($user, $foundUser) {
                $query->where('requester_id', $user->id)
                    ->where('addressee_id', $foundUser->id);
            })->orWhere(function ($query) use ($user, $foundUser) {
                $query->where('requester_id', $foundUser->id)
                    ->where('addressee_id', $user->id);
            })->first();

            $status = 'none';
            if ($friendship) {
                if ($friendship->status === 'accepted') {
                    $status = 'friends';
                } elseif ($friendship->status === 'pending') {
                    $status = $friendship->requester_id === $user->id ? 'pending_sent' : 'pending_received';
                }
            }

            return [
                'id' => $foundUser->id,
                'name' => $foundUser->name,
                'username' => $foundUser->username,
                'avatar' => $foundUser->avatar,
                'friendship_status' => $status
            ];
        });

        return response()->json([
            'success' => true,
            'users' => $usersWithStatus
        ]);
    }

    /**
     * Send a friend request
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $currentUser = Auth::user();
        $targetUserId = $request->input('user_id');

        // Check if user is trying to add themselves
        if ($currentUser->id == $targetUserId) {
            $msg = 'You cannot add yourself as a friend.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->with('error', $msg);
        }

        // Check if they are already friends
        $targetUser = User::find($targetUserId);
        if ($currentUser->isFriendWith($targetUser)) {
            $msg = 'You are already friends with this user.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->with('error', $msg);
        }

        // Check if a friendship record already exists
        $existingFriendship = \App\Models\Friendship::where(function ($query) use ($currentUser, $targetUserId) {
            $query->where('requester_id', $currentUser->id)
                ->where('addressee_id', $targetUserId);
        })->orWhere(function ($query) use ($currentUser, $targetUserId) {
            $query->where('requester_id', $targetUserId)
                ->where('addressee_id', $currentUser->id);
        })->first();

        if ($existingFriendship) {
            if ($existingFriendship->status === 'pending') {
                $msg = 'A friend request is already pending.';
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $msg], 422);
                }
                return back()->with('error', $msg);
            } elseif ($existingFriendship->status === 'declined') {
                // Update the declined request to pending
                $existingFriendship->update([
                    'requester_id' => $currentUser->id,
                    'addressee_id' => $targetUserId,
                    'status' => 'pending'
                ]);
                $msg = 'Friend request sent successfully.';
                if ($request->expectsJson()) {
                    return response()->json(['success' => true, 'message' => $msg]);
                }
                return back()->with('success', $msg);
            }
        }

        // Create new friend request
        \App\Models\Friendship::create([
            'requester_id' => $currentUser->id,
            'addressee_id' => $targetUserId,
            'status' => 'pending'
        ]);

        $msg = 'Friend request sent successfully.';
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }
        return back()->with('success', $msg);
    }

    /**
     * Accept a friend request
     */
    public function accept($friendshipId, Request $request)
    {
        $friendship = \App\Models\Friendship::findOrFail($friendshipId);
        $user = Auth::user();

        if ($friendship->addressee_id !== $user->id) {
            $msg = 'You are not authorized to accept this request.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 403);
            }
            return back()->with('error', $msg);
        }

        $friendship->update(['status' => 'accepted']);
        $msg = 'Friend request accepted!';
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }
        return back()->with('success', $msg);
    }

    /**
     * Decline a friend request
     */
    public function decline($friendshipId, Request $request)
    {
        $friendship = \App\Models\Friendship::findOrFail($friendshipId);
        $user = Auth::user();

        if ($friendship->addressee_id !== $user->id) {
            $msg = 'You are not authorized to decline this request.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 403);
            }
            return back()->with('error', $msg);
        }

        $friendship->update(['status' => 'declined']);
        $msg = 'Friend request declined.';
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }
        return back()->with('success', $msg);
    }

    /**
     * Cancel a sent friend request
     */
    public function cancel($friendshipId)
    {
        $friendship = \App\Models\Friendship::findOrFail($friendshipId);
        $user = Auth::user();

        if ($friendship->requester_id !== $user->id) {
            return back()->with('error', 'You are not authorized to cancel this request.');
        }

        $friendship->delete();
        return back()->with('success', 'Friend request cancelled.');
    }

    /**
     * Remove a friend
     */
    public function destroy(User $user)
    {
        $currentUser = Auth::user();

        if ($currentUser->id === $user->id) {
            return back()->with('error', 'You cannot remove yourself as a friend.');
        }

        // Find and delete the friendship record
        $friendship = \App\Models\Friendship::where(function ($query) use ($currentUser, $user) {
            $query->where('requester_id', $currentUser->id)
                ->where('addressee_id', $user->id);
        })->orWhere(function ($query) use ($currentUser, $user) {
            $query->where('requester_id', $user->id)
                ->where('addressee_id', $currentUser->id);
        })->first();

        if ($friendship) {
            $friendship->delete();
            return back()->with('success', 'Friend removed successfully.');
        }

        return back()->with('error', 'Friendship not found.');
    }

    /**
     * Block a user
     */
    public function block(User $user)
    {
        $currentUser = Auth::user();

        if ($currentUser->id === $user->id) {
            return back()->with('error', 'You cannot block yourself.');
        }

        // Find existing friendship record or create new one
        $friendship = \App\Models\Friendship::where(function ($query) use ($currentUser, $user) {
            $query->where('requester_id', $currentUser->id)
                ->where('addressee_id', $user->id);
        })->orWhere(function ($query) use ($currentUser, $user) {
            $query->where('requester_id', $user->id)
                ->where('addressee_id', $currentUser->id);
        })->first();

        if ($friendship) {
            $friendship->update([
                'requester_id' => $currentUser->id,
                'addressee_id' => $user->id,
                'status' => 'blocked'
            ]);
        } else {
            \App\Models\Friendship::create([
                'requester_id' => $currentUser->id,
                'addressee_id' => $user->id,
                'status' => 'blocked'
            ]);
        }

        return back()->with('success', 'User blocked successfully.');
    }

    /**
     * Get friends list for game invitations (API endpoint)
     */
    public function getFriendsForGame()
    {
        $user = Auth::user();

        $friends = $user->friends()
            ->select('id', 'username', 'name', 'avatar')
            ->get();

        return response()->json($friends);
    }
}
