<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class CommentController extends Controller
{
    
    public function comments(Post $post)
    {
        $comments = $post->comments()->get();

        return response()->json([ 'comments' => $comments ],200);
    } 
    public function store(Request $request, Post $post)
    {   
        $validated = $request->validate([
            'comment' => 'required|string',
        ]);

        $comment = $post->comments()->create([
            'user_id' => auth()->id(),
            'comment' => $validated['comment']
        ]);

        return response()->json([
            'comment' => $comment
        ],201);
    }

    

}
