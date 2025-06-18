<?php

namespace App\Blog;

use Illuminate\Support\ServiceProvider;
use App\Blog\UseCases\CreatePost;
use App\Blog\UseCases\UpdatePost;
use App\Blog\UseCases\DeletePost;
use App\Blog\UseCases\GetPost;
use App\Blog\UseCases\GetPosts;
use App\Blog\UseCases\CreateCategory;

class BlogServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(CreatePost::class);
        $this->app->bind(UpdatePost::class);
        $this->app->bind(DeletePost::class);
        $this->app->bind(GetPost::class);
        $this->app->bind(GetPosts::class);
        $this->app->bind(CreateCategory::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
    }
}
