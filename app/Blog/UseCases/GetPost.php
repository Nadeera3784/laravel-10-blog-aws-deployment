<?php

namespace App\Blog\UseCases;

use App\Blog\Entities\Post;
use App\Blog\UseCases\Exceptions\PostNotFoundException;

class GetPost
{
    public function execute(string $slug): Post
    {
        $post = Post::with(['category', 'user'])
            ->where('slug', $slug)
            ->first();

        if (!$post) {
            throw new PostNotFoundException("Post not found with slug: {$slug}");
        }

        return $post;
    }

    public function executeById(int $id): Post
    {
        $post = Post::with(['category', 'user'])->find($id);

        if (!$post) {
            throw new PostNotFoundException("Post not found with ID: {$id}");
        }

        return $post;
    }
}
