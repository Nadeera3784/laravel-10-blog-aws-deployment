<?php

namespace App\Blog\UseCases;

use App\Blog\Entities\Post;
use App\Blog\UseCases\Exceptions\PostNotFoundException;
use Illuminate\Support\Facades\Storage;

class DeletePost
{
    public function execute(int $id): bool
    {
        $post = Post::find($id);

        if (!$post) {
            throw new PostNotFoundException("Post not found: {$id}");
        }

        if ($post->image) {
            Storage::delete($post->image);
        }

        return $post->delete();
    }
}
