<?php

namespace App\Http\Controllers;

use App\Http\Requests\Posts\StoreRequest;
use App\Http\Requests\Posts\UpdateRequest;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\User;

class PostController extends Controller
{
    public function index(){
        $posts = Post::with('user')->orderBy('created_at', 'descgi')->get();
        $postsCount = Post::postsCount();
        $published = Post::published()->count();
        $drafts = Post::drafts()->count();
        return response()->json([
            'posts' => $posts,
            'postsCount' => $postsCount,
            'published' => $published,
            'drafts' => $drafts
        ]);
    }
    public function store(StoreRequest $request)
    {
        $validated = $request->validated();

        $imagePath = null;

        $status = $request->input('status', 'Published');

        if($request->hasFile('image')){
            $imagePath = $request->file('image')->store('blog/images', 'public');
        }

        $slug = Post::generateSlug($validated['title']);

        $post = $request->user()->posts()->create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'image' => $imagePath,
            'slug' => $slug,
            'status' => $status
        ]);

        ActivityLog::create([
            'type' => 'Post',
            'action' => $post->status,
            'description' => $post->title,
            'user_id' => auth()->id(),
            'meta' => [
                'post_id' => $post->id,
            ]
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
