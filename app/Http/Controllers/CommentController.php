<?php

namespace App\Http\Controllers;

use App\Http\Requests\Comments\StoreRequest;
use Illuminate\Http\Request;
use App\Models\Post;

class CommentController extends Controller
{
    
    public function comments(Post $post)
    {
        $comments = $post->comments()->get();

        return response()->json($comments);
    } 
    public function store(StoreRequest $request, Post $post)
    {   
            

        $comment = $post->comments()->create([
            'user_id' => auth()->id(),
            'comment' => $request->comment
        ]);

        return response()->json([
            'comment' => $comment
        ],201);
    }

    

}
