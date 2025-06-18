<?php

namespace App\Blog\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug'
    ];

    public function getPostCount(): int
    {
        return $this->posts()->count();
    }

    public function hasPublishedPosts(): bool
    {
        return $this->posts()->published()->exists();
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function scopeWithPosts($query)
    {
        return $query->has('posts');
    }

    public function scopeWithPublishedPosts($query)
    {
        return $query->whereHas('posts', function ($q) {
            $q->published();
        });
    }
}
