<?php

namespace App\Blog\IO\Events;

use App\Blog\Entities\Category;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CategoryUpdated
{
    use Dispatchable;
    use SerializesModels;

    public Category $category;

    public function __construct(Category $category)
    {
        $this->category = $category;
    }
}
