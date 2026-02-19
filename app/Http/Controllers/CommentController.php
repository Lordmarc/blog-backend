<?php

namespace App\Http\Controllers;

use App\Http\Requests\Comments\StoreRequest;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    
    public function comments(Post $post)
    {
        $comments = $post->comments()->whereNull('parent_id')->with(['user', 'replies.user'])->orderBy('created_at', 'desc')->get();

        return response()->json($comments);
    } 
    public function store(StoreRequest $request, Post $post)
    {   
            
        $comment = $post->comments()->create([
            'user_id' => auth()->id(),
            'comment' => $request->comment,
            'parent_id' => $request->parent_id
        ]);

        $comment->load('user');

          ActivityLog::create([
            'type' => 'Comment',
            'action' => 'Published',
            'description' => 'Commentended on' . ' ' . $post->title,
            'user_id' => auth()->id(),
            'meta' => [
                'post_id' => $post->id,
            ]
        ]);

        return response()->json($comment,201);
    }

    

}
