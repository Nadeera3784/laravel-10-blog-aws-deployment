<?php

namespace App\Blog\IO\Listeners;

use App\Blog\IO\Events\PostUpdated;
use App\Blog\IO\Jobs\UpdatePostIndexJob;

class PostUpdatedListener
{
    public function handle(PostUpdated $event): void
    {
        UpdatePostIndexJob::dispatch($event->post);
    }
}
