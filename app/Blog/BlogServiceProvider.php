<?php

namespace App\Blog;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Blog\UseCases\CreatePost;
use App\Blog\UseCases\UpdatePost;
use App\Blog\UseCases\DeletePost;
use App\Blog\UseCases\GetPost;
use App\Blog\UseCases\GetPosts;
use App\Blog\UseCases\GetPostsFromElasticsearch;
use App\Blog\UseCases\CreateCategory;
use App\Blog\UseCases\UpdateCategory;
use App\Blog\IO\Services\ElasticsearchService;
use App\Blog\Entities\Post;
use App\Blog\IO\Observers\PostObserver;
use App\Blog\IO\Events\PostCreated;
use App\Blog\IO\Events\PostUpdated;
use App\Blog\IO\Events\PostDeleted;
use App\Blog\IO\Listeners\PostCreatedListener;
use App\Blog\IO\Listeners\PostUpdatedListener;
use App\Blog\IO\Listeners\PostDeletedListener;
use App\Blog\Entities\Category;
use App\Blog\IO\Observers\CategoryObserver;
use App\Blog\IO\Events\CategoryUpdated;
use App\Blog\IO\Listeners\CategoryUpdatedListener;

class BlogServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerConfig();
        $this->app->bind(CreatePost::class);
        $this->app->bind(UpdatePost::class);
        $this->app->bind(DeletePost::class);
        $this->app->bind(GetPost::class);
        $this->app->bind(GetPosts::class);
        $this->app->bind(GetPostsFromElasticsearch::class);
        $this->app->bind(CreateCategory::class);
        $this->app->bind(UpdateCategory::class);
        $this->app->singleton(ElasticsearchService::class);
    }


    public function boot(): void
    {
        Post::observe(PostObserver::class);
        Category::observe(CategoryObserver::class);
        Event::listen(PostCreated::class, PostCreatedListener::class);
        Event::listen(PostUpdated::class, PostUpdatedListener::class);
        Event::listen(PostDeleted::class, PostDeletedListener::class);
        Event::listen(CategoryUpdated::class, CategoryUpdatedListener::class);
        if ($this->app->runningInConsole()) {
            $this->commands([
                \App\Blog\IO\Commands\RecreateElasticsearchIndex::class,
                \App\Blog\IO\Commands\RefreshElasticsearchIndex::class,
            ]);
        }
    }

    protected function registerConfig()
    {
        $this->mergeConfigFrom(__DIR__ . '/Specs/Config/elasticsearch.php', 'elasticsearch');
        $this->mergeConfigFrom(__DIR__ . '/Specs/Config/elasticsearch-post-index-template.php', 'elasticsearch-post-index-template');
    }
}
