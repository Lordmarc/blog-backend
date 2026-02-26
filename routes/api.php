<?php

use App\Http\Controllers\ActivityLogController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::prefix('v0')->group(function () {
        Route::get('/user-stats', [UserController::class, 'userCount']);
        Route::get('/recent-activity', [ActivityLogController::class, 'index']);
    });

    Route::prefix('v1')->group(function () {
        Route::get('/posts', [PostController::class, 'index']);
        Route::post('/posts', [PostController::class, 'store']);
        Route::get('/posts/blog', [PostController::class, 'showTopTags']);
        Route::post('/posts/{post}', [PostController::class, 'update']);
        Route::post('/posts/{post}/comment', [CommentController::class, 'store']);
        Route::get('/posts/{post}/comments', [CommentController::class, 'comments']);
       
        Route::delete('/posts/{post}', [PostController::class, 'destroy']);
        Route::get('/posts/slug/{slug}', [PostController::class, 'show']);
 
    });
});
