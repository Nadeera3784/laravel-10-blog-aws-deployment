<?php

namespace App\Blog\IO\Events;

use App\Blog\Entities\Post;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostCreated
{
    use Dispatchable;
    use SerializesModels;

    public Post $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }
}
