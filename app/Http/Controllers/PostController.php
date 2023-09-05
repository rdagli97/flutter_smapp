<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class PostController extends Controller
{
    // get all post

    public function index()
    {

        return response()->json([
            'posts' => Post::orderBy('created_at', 'desc')->with('user:id,username,email,image')->withCount('comments', 'likes')
                ->with('likes', function ($like) {
                    return $like->where('user_id', auth()->user()->id)->select('id', 'user_id', 'post_id')->get();
                })
                ->get()
        ]);
    }


    // store a post

    public function store(Request $request)
    {

        $attrs = $request->validate([
            'body' => 'required|string',
        ]);

        $post = Post::create([
            'body' => $attrs['body'],
            'user_id' => auth()->user()->id,
        ]);

        return response()->json([
            'message' => 'Post created successfully',
            'post' => $post,
        ], 200);
    }

    // get 1 post by postId
    public function getOnePost($id)
    {

        return response()->json([
            'post' => Post::where('id', $id)->with('user:id,username,email,image')->withCount('comments', 'likes')
                ->with('likes', function ($like) {
                    return $like->where('user_id', auth()->user()->id)->select('id', 'user_id', 'post_id')->get();
                })->get(),
        ], 200);
    }

    // delete a post

    public function destroy($id)
    {

        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'message' => 'Post not found',
            ], 404);
        }

        if ($post->user_id != auth()->user()->id) {
            return response()->json([
                'message' => 'Permission denied',
            ], 403);
        }

        $post->likes()->delete();
        $post->comments()->delete();
        $post->delete();

        return response()->json([
            'message' => 'Post deleted successfully',
        ], 200);
    }

    // update a post

    public function update(Request $request, $id)
    {

        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'message' => 'Post not found'
            ], 404);
        }

        if ($post->user_id != auth()->user()->id) {
            return response()->json([
                'message' => 'Permission denied',
            ], 403);
        }

        $attrs = $request->validate([
            'body' => 'required|string',
        ]);

        $post->update([
            'body' => $attrs['body'],
        ]);

        return response()->json([
            'message' => 'Post updated',
            'post' => $post,
        ], 200);
    }

    // get just followings posts

    public function getFollowingPosts()
    {

        $currentUser = User::find(auth()->user()->id);
        $followingIds = $currentUser->followings()->pluck('following_id');

        $posts = Post::whereIn('user_id', $followingIds)->orderBy('created_at', 'desc')->with('user:id,username,email,image')->withCount('comments', 'likes')
            ->with('likes', function ($like) {
                return $like->where('user_id', auth()->user()->id)->select('id', 'user_id', 'post_id')->get();
            })->get();

        if (!$posts->count() > 0) {
            return response()->json([
                'message' => 'There is no post to show yet...',
            ], 404);
        }

        return response()->json([
            'posts' => $posts,
        ], 200);
    }

    // current user's posts
    public function currentUserPosts()
    {

        $posts = Post::where('user_id', auth()->user()->id)->orderBy('created_at', 'desc')->with('user:id,username,email,image')->withCount('comments', 'likes')
            ->with('likes', function ($like) {
                return $like->where('user_id', auth()->user()->id)->select('id', 'user_id', 'post_id')->get();
            })->get();

        if (!$posts) {
            return response()->json([
                'message' => 'There is no post to show yet...',
            ], 404);
        }

        return response()->json([
            'posts' => $posts,
        ], 200);
    }

    // get user by id and show it's posts
    public function getOneUsersPosts($id)
    {

        $posts = Post::where('user_id', $id)->orderBy('created_at', 'desc')->with('user:id,username,email,image')->withCount('comments', 'likes')
            ->with('likes', function ($like) {
                return $like->where('user_id', auth()->user()->id)->select('id', 'user_id', 'post_id')->get();
            })->get();

        if (!$posts) {
            return response()->json([
                'message' => 'There is no post to show yet...'
            ], 404);
        }

        return response()->json([
            'posts' => $posts,
        ], 200);
    }

    // get liked posts by userId

    public function getLikedPostsByUserId($id)
    {

        $user = User::find($id);
        $likedPostsIds = $user->likes()->pluck('post_id');

        $posts = Post::whereIn('id', $likedPostsIds)->orderBy('created_at', 'desc')->with('user:id,username,email,image')->withCount('comments', 'likes')
            ->with('likes', function ($like) {
                return $like->where('user_id', auth()->user()->id)->select('id', 'user_id', 'post_id')->get();
            })->get();

        if (!$posts->count() > 0) {
            return response()->json([
                'message' => 'There is no post to show yet...',
            ], 404);
        }

        return response()->json([
            'posts' => $posts,
        ], 200);
    }

    // get current user liked post list
    public function getCurrentUsersLikedPosts()
    {

        $user = User::find(auth()->user()->id);
        $likedPostsIds = $user->likes()->pluck('post_id');

        $posts = Post::whereIn('id', $likedPostsIds)->orderBy('created_at', 'desc')->with('user:id,username,email,image')->withCount('comments', 'likes')
            ->with('likes', function ($like) {
                return $like->where('user_id', auth()->user()->id)->select('id', 'user_id', 'post_id')->get();
            })->get();

        if (!$posts->count() > 0) {
            return response()->json([
                'message' => 'There is no liked post to show yet...',
            ], 404);
        }

        return response()->json([
            'posts' => $posts,
        ], 200);
    }
}
