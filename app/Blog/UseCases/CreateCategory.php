<?php

namespace App\Blog\UseCases;

use App\Blog\Entities\Category;
use App\Blog\UseCases\Exceptions\CategoryCreationException;
use Illuminate\Support\Str;

class CreateCategory
{
    public function execute(array $data): Category
    {
        try {
            $category = new Category;
            $category->name = $data['name'];
            $category->slug = Str::slug($data['name']);

            $category->save();

            return $category;
        } catch (\Exception $e) {
            throw new CategoryCreationException('Failed to create category: ' . $e->getMessage());
        }
    }
}
