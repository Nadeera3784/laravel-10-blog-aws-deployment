<?php

namespace App\Blog\IO\Events;

use App\Blog\Entities\Post;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostDeleted
{
    use Dispatchable;
    use SerializesModels;

    public Post $post;
    public int $postId;

    public function __construct(Post $post)
    {
        $this->post = $post;
        $this->postId = $post->id;
    }
}
