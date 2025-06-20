<?php

namespace App\Blog\IO\Listeners;

use App\Blog\IO\Events\PostDeleted;
use App\Blog\IO\Jobs\DeletePostIndexJob;

class PostDeletedListener
{
    public function handle(PostDeleted $event): void
    {
        DeletePostIndexJob::dispatch($event->postId);
    }
}
