<?php

namespace App\Blog\IO\Observers;

use App\Blog\Entities\Category;
use App\Blog\IO\Events\CategoryUpdated;

class CategoryObserver
{
    public function updated(Category $category)
    {
        event(new CategoryUpdated($category));
    }
}
