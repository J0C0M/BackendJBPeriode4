<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Game;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    /**
     * Store a new comment
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'commentable_type' => 'required|in:App\Models\Game,App\Models\User',
            'commentable_id' => 'required|integer',
            'content' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Verify the commentable entity exists
        $commentableType = $request->commentable_type;
        $commentableId = $request->commentable_id;

        if ($commentableType === 'App\Models\Game') {
            $commentable = Game::find($commentableId);
        } elseif ($commentableType === 'App\Models\User') {
            $commentable = User::find($commentableId);
        }

        if (!$commentable) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'The item you are trying to comment on does not exist.'
                ], 404);
            }

            return redirect()->back()
                ->with('error', 'The item you are trying to comment on does not exist.');
        }

        // Check permissions
        if (!$this->canComment(Auth::user(), $commentable)) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to comment on this item.'
                ], 403);
            }

            return redirect()->back()
                ->with('error', 'You do not have permission to comment on this item.');
        }

        $comment = Comment::create([
            'user_id' => Auth::id(),
            'commentable_type' => $commentableType,
            'commentable_id' => $commentableId,
            'content' => $request->content,
            'is_approved' => true, // Auto-approve for now
        ]);

        $comment->load('user');

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Comment posted successfully!',
                'comment' => [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'created_at' => $comment->created_at->diffForHumans(),
                    'user' => [
                        'name' => $comment->user->name,
                        'username' => $comment->user->username,
                        'avatar' => $comment->user->avatar,
                    ]
                ]
            ]);
        }

        return redirect()->back()
            ->with('success', 'Comment posted successfully!');
    }

    /**
     * Get comments for a specific item
     */
    public function getComments(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'commentable_type' => 'required|in:App\Models\Game,App\Models\User',
            'commentable_id' => 'required|integer',
            'page' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $comments = Comment::where('commentable_type', $request->commentable_type)
            ->where('commentable_id', $request->commentable_id)
            ->where('is_approved', true)
            ->with('user:id,name,username,avatar')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'comments' => $comments->items(),
            'pagination' => [
                'current_page' => $comments->currentPage(),
                'last_page' => $comments->lastPage(),
                'per_page' => $comments->perPage(),
                'total' => $comments->total(),
            ]
        ]);
    }

    /**
     * Update a comment
     */
    public function update(Request $request, Comment $comment)
    {
        // Check if user owns the comment
        if ($comment->user_id !== Auth::id()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only edit your own comments.'
                ], 403);
            }

            return redirect()->back()
                ->with('error', 'You can only edit your own comments.');
        }

        $validator = Validator::make($request->all(), [
            'content' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $comment->update([
            'content' => $request->content,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Comment updated successfully!',
                'comment' => [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'updated_at' => $comment->updated_at->diffForHumans(),
                ]
            ]);
        }

        return redirect()->back()
            ->with('success', 'Comment updated successfully!');
    }

    /**
     * Delete a comment
     */
    public function destroy(Request $request, Comment $comment)
    {
        // Check if user owns the comment or is admin
        if ($comment->user_id !== Auth::id()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only delete your own comments.'
                ], 403);
            }

            return redirect()->back()
                ->with('error', 'You can only delete your own comments.');
        }

        $comment->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Comment deleted successfully!'
            ]);
        }

        return redirect()->back()
            ->with('success', 'Comment deleted successfully!');
    }

    /**
     * Toggle comment approval (admin function)
     */
    public function toggleApproval(Comment $comment)
    {
        // This would typically have admin middleware
        $comment->update([
            'is_approved' => !$comment->is_approved
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Comment approval status updated!',
            'is_approved' => $comment->is_approved
        ]);
    }

    /**
     * Get comments for user profile
     */
    public function getUserComments(User $user)
    {
        $comments = Comment::where('commentable_type', 'App\Models\User')
            ->where('commentable_id', $user->id)
            ->where('is_approved', true)
            ->with('user:id,name,username,avatar')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'comments' => $comments->items(),
            'pagination' => [
                'current_page' => $comments->currentPage(),
                'last_page' => $comments->lastPage(),
                'per_page' => $comments->perPage(),
                'total' => $comments->total(),
            ]
        ]);
    }

    /**
     * Get comments for a specific game
     */
    public function getGameComments(Game $game)
    {
        $comments = Comment::where('commentable_type', 'App\Models\Game')
            ->where('commentable_id', $game->id)
            ->where('is_approved', true)
            ->with('user:id,name,username,avatar')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'comments' => $comments->items(),
            'pagination' => [
                'current_page' => $comments->currentPage(),
                'last_page' => $comments->lastPage(),
                'per_page' => $comments->perPage(),
                'total' => $comments->total(),
            ]
        ]);
    }

    /**
     * Check if user can comment on the given item
     */
    private function canComment(User $user, $commentable): bool
    {
        if ($commentable instanceof Game) {
            // Can comment if user is part of the game or if game is completed
            return $commentable->isPlayerInGame($user) || $commentable->status === 'completed';
        }

        if ($commentable instanceof User) {
            // Check privacy settings
            $settings = $commentable->settings;
            if (!$settings) {
                return true; // Default to public if no settings
            }

            switch ($settings->privacy_level) {
                case 'private':
                    return $user->id === $commentable->id;
                case 'friends_only':
                    return $user->isFriendWith($commentable) || $user->id === $commentable->id;
                case 'public':
                default:
                    return true;
            }
        }

        return false;
    }
}
