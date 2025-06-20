<?php

namespace App\Blog\IO\Listeners;

use App\Blog\IO\Events\CategoryUpdated;
use App\Blog\IO\Jobs\UpdateCategoryPostsJob;

class CategoryUpdatedListener
{
    public function handle(CategoryUpdated $event): void
    {
        UpdateCategoryPostsJob::dispatch($event->category);
    }
}
