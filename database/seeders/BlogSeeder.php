<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Blog\Entities\Category;
use App\Blog\Entities\Post;
use Illuminate\Support\Facades\Hash;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Create a test user
        $user = User::create([
            'name' => 'Blog Admin',
            'email' => 'admin@blog.com',
            'password' => Hash::make('password'),
        ]);

        // Create categories
        $categories = [
            ['name' => 'Technology', 'slug' => 'technology'],
            ['name' => 'Travel', 'slug' => 'travel'],
            ['name' => 'Food', 'slug' => 'food'],
            ['name' => 'Lifestyle', 'slug' => 'lifestyle'],
        ];

        foreach ($categories as $categoryData) {
            $category = Category::create($categoryData);

            // Create sample posts for each category
            for ($i = 1; $i <= 3; $i++) {
                Post::create([
                    'name' => ucfirst($categoryData['name']) . " Post {$i}",
                    'slug' => $categoryData['slug'] . "-post-{$i}",
                    'description' => "This is a sample blog post about {$categoryData['name']}. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.\n\nDuis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.\n\nSed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.",
                    'category_id' => $category->id,
                    'user_id' => $user->id,
                    'is_published' => $i <= 2, // First 2 posts are published, 3rd is draft
                    'created_at' => now()->subDays(rand(1, 30)),
                ]);
            }
        }
    }
}
