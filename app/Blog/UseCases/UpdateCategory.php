<?php

namespace App\Blog\UseCases;

use App\Blog\Entities\Category;
use App\Blog\UseCases\Exceptions\CategoryCreationException;
use Illuminate\Support\Str;

class UpdateCategory
{
    public function execute(Category $category, array $data): Category
    {
        try {
            $category->update([
                'name' => $data['name'],
                'slug' => Str::slug($data['name'])
            ]);

            return $category->fresh();
        } catch (\Exception $e) {
            throw new CategoryCreationException('Failed to update category: ' . $e->getMessage());
        }
    }
} 