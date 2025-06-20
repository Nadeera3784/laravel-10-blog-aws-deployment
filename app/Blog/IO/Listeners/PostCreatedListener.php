<?php

namespace App\Blog\IO\Listeners;

use App\Blog\IO\Events\PostCreated;
use App\Blog\IO\Jobs\IndexPostJob;

class PostCreatedListener
{
    public function handle(PostCreated $event): void
    {
        IndexPostJob::dispatch($event->post);
    }
}
