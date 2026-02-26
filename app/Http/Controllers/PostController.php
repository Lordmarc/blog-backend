<?php

namespace App\Http\Controllers;

use App\Http\Requests\Posts\StoreRequest;
use App\Http\Requests\Posts\UpdateRequest;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\User;
use App\Models\Tag;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PostController extends Controller
{
    use AuthorizesRequests;
    public function index(){
        $posts = Post::with('user')->orderBy('created_at', 'desc')->get();
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

          if($request->has('tags')){
            $tagsId = [];

            foreach($request->tags as $tagName){
                $tag = Tag::firstOrCreate([
                    'name' => strtolower($tagName)
                ]);

                $tagsId[] = $tag->id;
            }

            $post->tags()->sync($tagsId);
        }

        ActivityLog::create([
            'type' => 'Post',
            'action' => $post->status,
            'description' => $post->title,
            'user_id' => auth()->id(),
            'meta' => [
                'post_id' => $post->id,
            ]
        ]);

        return response()->json($post->load('tags'), 201);
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

  public function destroy(Post $post)
{
    \Log::info('User role and ownership', [
        'user_id' => auth()->id(),
        'user_role' => auth()->user()->role,
        'post_user_id' => $post->user_id,
    ]);

    $this->authorize('delete', $post);

    if ($post->image) {
        Storage::disk('public')->delete($post->image);
    }

    $post->delete();

    return response()->json(['success' => 'Post deleted successfully!']);
}

    public function show($slug){

    $post = Post::with('user', 'tags') -> where('slug', $slug)->first();

    if(!$post){
        return response()->json(['error' => 'Post not found'], 404);
    }

    return response()->json($post);
    }

    public function showTopTags(){
        $popularTags = Cache::remember('tags.top.5', 60, function(){
        return Tag::withCount('posts')
                ->orderBy('posts_count','desc')
                ->take(5)
                ->get(['id', 'name']);

        
        });
        return response()->json([
            'success' => true,
            'data' => $popularTags
        ]);
    }
}
