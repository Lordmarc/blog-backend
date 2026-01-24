<?php

namespace App\Http\Controllers;

use App\Http\Requests\Posts\StoreRequest;
use App\Http\Requests\Posts\UpdateRequest;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\User;

class PostController extends Controller
{
    public function index(){
        $posts = Post::all();
        
        return response()->json($posts);
    }
    public function store(StoreRequest $request)
    {
        $validated = $request->validated();

        $slug = Post::generateSlug($validated['title']);

        $post = $request->user()->posts()->create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'slug' => $slug,
            'status' => 'published'
        ]);

        return response()->json($post, 201);
    }

    public function update(UpdateRequest $request, Post $post)
    {
        $this->authorize('update', $post);
        $validated = $request->validated();

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
