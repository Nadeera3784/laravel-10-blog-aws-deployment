<?php

namespace App\Blog\UseCases;

use App\Blog\Entities\Post;
use Illuminate\Pagination\LengthAwarePaginator;

class GetPosts
{
    public function execute(?int $categoryId = null, bool $publishedOnly = false, int $perPage = 9): LengthAwarePaginator
    {
        $query = Post::with(['category', 'user'])
            ->orderBy('created_at', 'desc');

        if ($publishedOnly) {
            $query->published();
        }

        if ($categoryId) {
            $query->byCategory($categoryId);
        }

        return $query->paginate($perPage);
    }
}
