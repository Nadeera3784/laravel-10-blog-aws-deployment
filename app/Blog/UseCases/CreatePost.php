<?php

namespace App\Blog\UseCases;

use App\Blog\Entities\Post;
use App\Blog\UseCases\Exceptions\PostCreationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\UploadedFile;

class CreatePost
{
    public function execute(array $data): Post
    {
        try {
            $post = new Post;
            $post->name = $data['name'];
            $post->description = $data['description'];
            $post->category_id = $data['category_id'];
            $post->user_id = Auth::id();
            $post->is_published = $data['is_published'] ?? false;

            $post->generateSlug();

            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                $imagePath = $data['image']->storeAs(
                    'posts',
                    time() . '_' . $data['image']->getClientOriginalName()
                );
                $post->image = $imagePath;
            }

            $post->save();

            return $post;
        } catch (\Exception $e) {
            throw new PostCreationException('Failed to create post: ' . $e->getMessage());
        }
    }
}
