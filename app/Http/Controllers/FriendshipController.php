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

        // Get accepted friends
        $friends = $user->friends()->with('settings')->get();

        // Get pending sent requests
        $sentRequests = $user->sentFriendRequests()
            ->where('status', 'pending')
            ->with('addressee')
            ->get();

        // Get pending received requests
        $receivedRequests = $user->receivedFriendRequests()
            ->where('status', 'pending')
            ->with('requester')
            ->get();

        return view('friends.index', compact('friends', 'sentRequests', 'receivedRequests'));
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
            'query' => 'required|string|min:2'
        ]);

        $query = $request->input('query');
        $currentUser = Auth::user();

        $users = User::where('id', '!=', $currentUser->id)
            ->where(function ($q) use ($query) {
                $q->where('username', 'LIKE', "%{$query}%")
                    ->orWhere('email', 'LIKE', "%{$query}%")
                    ->orWhere('name', 'LIKE', "%{$query}%");
            })
            ->limit(20)
            ->get();

        // Add friendship status to each user
        $users->each(function ($user) use ($currentUser) {
            if ($currentUser->isFriendWith($user)) {
                $user->friendship_status = 'friends';
            } elseif ($currentUser->hasSentFriendRequestTo($user)) {
                $user->friendship_status = 'request_sent';
            } elseif ($currentUser->hasPendingFriendRequestFrom($user)) {
                $user->friendship_status = 'request_received';
            } else {
                $user->friendship_status = 'none';
            }
        });

        return response()->json($users);
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
            return back()->with('error', 'You cannot add yourself as a friend.');
        }

        // Check if they are already friends
        if ($currentUser->isFriendWith(User::find($targetUserId))) {
            return back()->with('error', 'You are already friends with this user.');
        }

        // Check if a friendship record already exists
        $existingFriendship = Friendship::where(function ($query) use ($currentUser, $targetUserId) {
            $query->where('requester_id', $currentUser->id)
                ->where('addressee_id', $targetUserId);
        })->orWhere(function ($query) use ($currentUser, $targetUserId) {
            $query->where('requester_id', $targetUserId)
                ->where('addressee_id', $currentUser->id);
        })->first();

        if ($existingFriendship) {
            if ($existingFriendship->status === 'pending') {
                return back()->with('error', 'A friend request is already pending.');
            } elseif ($existingFriendship->status === 'declined') {
                // Update the declined request to pending
                $existingFriendship->update([
                    'requester_id' => $currentUser->id,
                    'addressee_id' => $targetUserId,
                    'status' => 'pending'
                ]);
                return back()->with('success', 'Friend request sent successfully.');
            }
        }

        // Create new friend request
        Friendship::create([
            'requester_id' => $currentUser->id,
            'addressee_id' => $targetUserId,
            'status' => 'pending'
        ]);

        return back()->with('success', 'Friend request sent successfully.');
    }

    /**
     * Accept a friend request
     */
    public function accept(Friendship $friendship)
    {
        $currentUser = Auth::user();

        // Check if the current user is the addressee
        if ($friendship->addressee_id !== $currentUser->id) {
            return back()->with('error', 'You are not authorized to accept this request.');
        }

        // Check if the request is still pending
        if ($friendship->status !== 'pending') {
            return back()->with('error', 'This friend request is no longer pending.');
        }

        $friendship->update(['status' => 'accepted']);

        return back()->with('success', 'Friend request accepted successfully.');
    }

    /**
     * Decline a friend request
     */
    public function decline(Friendship $friendship)
    {
        $currentUser = Auth::user();

        // Check if the current user is the addressee
        if ($friendship->addressee_id !== $currentUser->id) {
            return back()->with('error', 'You are not authorized to decline this request.');
        }

        // Check if the request is still pending
        if ($friendship->status !== 'pending') {
            return back()->with('error', 'This friend request is no longer pending.');
        }

        $friendship->update(['status' => 'declined']);

        return back()->with('success', 'Friend request declined.');
    }

    /**
     * Cancel a sent friend request
     */
    public function cancel(Friendship $friendship)
    {
        $currentUser = Auth::user();

        // Check if the current user is the requester
        if ($friendship->requester_id !== $currentUser->id) {
            return back()->with('error', 'You are not authorized to cancel this request.');
        }

        // Check if the request is still pending
        if ($friendship->status !== 'pending') {
            return back()->with('error', 'This friend request is no longer pending.');
        }

        $friendship->delete();

        return back()->with('success', 'Friend request cancelled.');
    }

    /**
     * Remove a friend
     */
    public function destroy(Request $request, User $user)
    {
        $currentUser = Auth::user();

        // Find the friendship record
        $friendship = Friendship::where(function ($query) use ($currentUser, $user) {
            $query->where('requester_id', $currentUser->id)
                ->where('addressee_id', $user->id);
        })->orWhere(function ($query) use ($currentUser, $user) {
            $query->where('requester_id', $user->id)
                ->where('addressee_id', $currentUser->id);
        })->where('status', 'accepted')->first();

        if (!$friendship) {
            return back()->with('error', 'You are not friends with this user.');
        }

        $friendship->delete();

        return back()->with('success', 'Friend removed successfully.');
    }

    /**
     * Block a user
     */
    public function block(Request $request, User $user)
    {
        $currentUser = Auth::user();

        if ($currentUser->id === $user->id) {
            return back()->with('error', 'You cannot block yourself.');
        }

        // Find existing friendship record or create new one
        $friendship = Friendship::where(function ($query) use ($currentUser, $user) {
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
            Friendship::create([
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
