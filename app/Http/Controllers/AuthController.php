<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // register()
    public function register(Request $request)
    {

        $attrs = $request->validate([
            'username' => 'required|string|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'image' => 'required|string',
        ]);

        $image = $this->saveImage($request->image, 'profiles');

        $user = User::create([
            'username' => $attrs['username'],
            'email' => $attrs['email'],
            'password' => bcrypt($attrs['password']),
            'image' => $image,
        ]);

        return response()->json([
            'user' => $user,
            'token' => $user->createToken('secret')->plainTextToken,
        ], 200);
    }

    // login()
    public function login(Request $request)
    {

        $attrs = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (!Auth::attempt($attrs)) {
            return response()->json([
                'message' => 'Invalid credentails',
            ], 403);
        }

        return response()->json([
            'user' => auth()->user(),
            'token' => auth()->user()->createToken('secret')->plainTextToken,
        ], 200);
    }

    // get current user detail
    public function getUser()
    {
        return response()->json([
            'user' => User::where('id', auth()->user()->id)->withCount('posts', 'followers', 'followings')->first(),
        ], 200);
    }

    // show 1 user by id
    public function show($id)
    {
        $user = User::where('id', $id)->withCount('posts', 'followers', 'followings')->first();

        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        return response()->json([
            'user' => $user,
        ]);
    }

    // update user

    public function update(Request $request)
    {
        $attrs = $request->validate([
            'image' => 'required|string',
        ]);

        $image = $this->saveImage($request->image, 'profiles');

        auth()->user()->update([
            'image' => $image,
        ]);

        return response()->json([
            'message' => 'User updated',
            'user' => auth()->user(),
        ], 200);
    }

    // logout user
    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response([
            'message' => 'Logout success.',
        ], 200);
    }

    // get following users
    public function getFollowings($id)
    {

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'There is no user',
            ], 405);
        }

        $followingIds = $user->followings()->pluck('following_id');

        $followingUsers = User::whereIn('id', $followingIds)->orderBy('created_at', 'desc')->get();

        if (!$followingUsers->count() > 0) {
            return response()->json([
                'message' => 'There is no user to show yet...',
            ], 404);
        }

        return response([
            'users' => $followingUsers,
        ], 200);
    }

    // get followers users
    public function getFollowers($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'There is no user',
            ], 405);
        }

        $followerIds = $user->followers()->pluck('follower_id');
        $followerUsers = User::whereIn('id', $followerIds)->get();

        if (!$followerUsers->count() > 0) {
            return response()->json([
                'message' => 'There is no user to show yet...',
            ], 404);
        }

        return response()->json([
            'users' => $followerUsers,
        ], 200);
    }

    // get user's followers ids 
    public function getFollowerIds($id)
    {

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'There is no user',
            ], 405);
        }

        $followerIds = $user->followers()->pluck('follower_id');

        if (!$followerIds) {
            return response()->json([
                'message' => 'There is no follower yet...',
            ], 404);
        }

        return response()->json([
            'follower_ids' => $followerIds,
        ], 200);
    }
}
