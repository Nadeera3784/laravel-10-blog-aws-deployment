<?php

namespace App\Blog\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'category_id',
        'user_id',
        'is_published'
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function publish(): self
    {
        $this->is_published = true;
        return $this;
    }

    public function unpublish(): self
    {
        $this->is_published = false;
        return $this;
    }

    public function isPublished(): bool
    {
        return $this->is_published;
    }

    public function generateSlug(): self
    {
        $this->slug = Str::slug($this->name);
        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->image ? Storage::url($this->image) : null;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }
}
