<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    // get comments of a post

    public function index($id)
    {

        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'message' => 'Post not found',
            ], 404);
        }

        return response()->json([
            'comments' => $post->comments()->with('user:id,username,email,image')->get(),
        ], 200);
    }

    // create a comment

    public function store(Request $request, $id)
    {

        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'message' => 'Post not found',
            ], 404);
        }

        $attrs = $request->validate([
            'comment' => 'required|string',
        ]);

        $comment = Comment::create([
            'user_id' => auth()->user()->id,
            'post_id' => $id,
            'comment' => $attrs['comment'],
        ]);

        return response()->json([
            'message' => 'Comment created successfully',
            'comment' => $comment,
        ], 200);
    }

    // delete a comment

    public function destroy($id)
    {

        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json([
                'message' => 'Comment not found',
            ], 404);
        }

        if ($comment->user_id != auth()->user()->id) {
            return response()->json([
                'message' => 'Permission denied',
            ], 403);
        }

        $comment->delete();

        return response()->json([
            'message' => 'Comment deleted successfully',
        ], 200);
    }

    // update a comment
    public function update(Request $request, $id)
    {

        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json([
                'message' => 'Comment not found',
            ], 404);
        }

        if ($comment->user_id != auth()->user()->id) {
            return response()->json([
                'message' => 'Permission denied',
            ], 403);
        }

        $attrs = $request->validate([
            'comment' => 'required|string',
        ]);

        $comment->update([
            'comment' => $attrs['comment'],
        ]);

        return response()->json([
            'message' => 'Comment updated success',
            'comment' => $comment,
        ], 200);
    }
}
