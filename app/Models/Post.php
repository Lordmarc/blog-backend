<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'image',
        'content',
        'status',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function comments(){
        return $this->hasMany(Comment::class);
    }

    public function tags(){
        return $this->belongsToMany(Tag::class);
    }
    

    public static function generateSlug($title)
    {
        $slug = Str::slug($title);
        $count = static::where('slug', 'LIKE', "{$slug}%")->count();

        if($count > 0)
        {
            $slug .= '-' . ($count+1);
        }
        
        return $slug;
    }

    public function scopePostsCount($query){
        return $query->count();
    }

    public function scopePublished($query){
        return $query->where('status', 'published');
    }

    public function scopeDrafts($query){
        return $query->where('status', 'draft');
    }
}
