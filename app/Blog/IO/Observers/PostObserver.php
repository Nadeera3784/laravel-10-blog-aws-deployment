<?php

namespace App\Blog\IO\Observers;

use App\Blog\Entities\Post;
use App\Blog\IO\Events\PostCreated;
use App\Blog\IO\Events\PostUpdated;
use App\Blog\IO\Events\PostDeleted;

class PostObserver
{
    public function created(Post $post)
    {
        event(new PostCreated($post));
    }

    public function updated(Post $post)
    {
        event(new PostUpdated($post));
    }

    public function deleted(Post $post)
    {
        event(new PostDeleted($post));
    }
}
