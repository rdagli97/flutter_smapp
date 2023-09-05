<?php

namespace App\Http\Controllers;

use App\Models\Follower;
use App\Models\User;
use Illuminate\Http\Request;

class FollowerController extends Controller
{
    // add followers of an user

    public function followOrUnFollow($id)
    {
        if ($id == auth()->user()->id) {
            return response()->json([
                'message' => 'You can not follow yourself'
            ], 405);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        $follower = $user->followers()->where('follower_id', auth()->user()->id)->first();

        if (!$follower) {
            Follower::create([
                'follower_id' => auth()->user()->id,
                'following_id' => $id,
            ]);

            return response()->json([
                'message' => 'Followed',
                'follower_id' => auth()->user()->id,
                'following_id' => $id,
            ], 200);
        }

        $follower->delete();

        return response()->json([
            'message' => 'Unfollowed',
        ], 200);
    }
}
