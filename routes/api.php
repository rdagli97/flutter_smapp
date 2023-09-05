<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatroomController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FollowerController;
use App\Http\Controllers\FollowingController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PostController;
use App\Models\Follower;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {

    // User
    Route::get('/users/{id}', [AuthController::class, 'show']);
    Route::put('/users', [AuthController::class, 'update']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/users/{id}/followings', [AuthController::class, 'getFollowings']);
    Route::get('/users/{id}/followers', [AuthController::class, 'getFollowers']);
    Route::get('/user', [AuthController::class, 'getUser']);
    Route::get('/user/{id}/followerIds', [AuthController::class, 'getFollowerIds']);
    // Post
    Route::get('/posts', [PostController::class, 'index']);
    Route::get('posts/{id}', [PostController::class, 'getOnePost']);
    Route::get('/users/{id}/posts', [PostController::class, 'getOneUsersPosts']);
    Route::post('/posts', [PostController::class, 'store']);
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);
    Route::put('/posts/{id}', [PostController::class, 'update']);
    Route::get('/followings/posts', [PostController::class, 'getFollowingPosts']);
    Route::get('/currentUser/posts', [PostController::class, 'currentUserPosts']);
    Route::get('/users/{id}/likes/posts', [PostController::class, 'getLikedPostsByUserId']);
    Route::get('/currentUser/likes/posts', [PostController::class, 'getCurrentUsersLikedPosts']);
    // Comment
    Route::get('/posts/{id}/comments', [CommentController::class, 'index']);
    Route::post('/posts/{id}/comments', [CommentController::class, 'store']);
    Route::delete('/comments/{id}', [CommentController::class, 'destroy']);
    Route::put('/comments/{id}', [CommentController::class, 'update']);
    // Like
    Route::post('/posts/{id}/likes', [LikeController::class, 'likeOrUnlike']);
    // Follow
    Route::post('/users/{id}/follow', [FollowerController::class, 'followOrUnFollow']);
});
