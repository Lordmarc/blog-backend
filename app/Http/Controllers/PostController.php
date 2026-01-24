<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\User;

class PostController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required',
            'content' => 'required'
        ]);

        $slug = Post::generateSlug($validated['title']);

        $post = $request->user()->posts()->create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'slug' => $slug,
            'status' => 'published'
        ]);

        return response()->json($post, 201);
    }

    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => 'nullable|string',
            'content' => 'nullable|string',
        ]);

        if(isset($validated['title'])){
            $validated['slug'] = Post::generateSlug($validated['title']);
        }

        $post->update($validated);

        return response()->json([
            'success' => 'Updated Successfully!',
            'post' => $post->fresh(),
        ],200);
    }
}
