<?php

namespace App\Blog\UseCases;

use App\Blog\Entities\Post;
use App\Blog\UseCases\Exceptions\PostNotFoundException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class UpdatePost
{
    public function execute(int $id, array $data): Post
    {
        $post = Post::find($id);

        if (!$post) {
            throw new PostNotFoundException("Post not found: {$id}");
        }

        $post->name = $data['name'];
        $post->description = $data['description'];
        $post->category_id = $data['category_id'];
        $post->is_published = $data['is_published'] ?? $post->is_published;

        if ($post->isDirty('name')) {
            $post->generateSlug();
        }

        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            if ($post->image) {
                Storage::delete($post->image);
            }

            $imagePath = $data['image']->storeAs(
                'posts',
                time() . '_' . $data['image']->getClientOriginalName()
            );
            $post->image = $imagePath;
        }

        $post->save();

        return $post;
    }
}
