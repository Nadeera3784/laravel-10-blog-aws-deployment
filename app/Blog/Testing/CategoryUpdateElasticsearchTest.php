<?php

namespace App\Blog\Testing;

use Tests\TestCase;
use App\Blog\Entities\Category;
use App\Blog\Entities\Post;
use App\Models\User;
use App\Blog\IO\Services\ElasticsearchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

class CategoryUpdateElasticsearchTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private ElasticsearchService $elasticsearch;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->elasticsearch = app(ElasticsearchService::class);
    }

    public function it_updates_posts_in_elasticsearch_when_category_name_changes()
    {
        Queue::fake();

        $category = Category::create(['name' => 'Original Tech', 'slug' => 'original-tech']);

        $post = Post::create([
            'name' => 'Test Post',
            'slug' => 'test-post',
            'description' => 'Test description',
            'category_id' => $category->id,
            'user_id' => $this->user->id,
            'is_published' => true
        ]);

        $category->update(['name' => 'Updated Tech Category']);

        Queue::assertPushed(\App\Blog\IO\Jobs\UpdateCategoryPostsJob::class, function ($job) use ($category) {
            return $job->category->id === $category->id;
        });
    }

    public function it_processes_category_update_job_correctly()
    {
        $category = Category::create(['name' => 'Science', 'slug' => 'science']);

        $post1 = Post::create([
            'name' => 'Post 1',
            'slug' => 'post-1',
            'description' => 'Description 1',
            'category_id' => $category->id,
            'user_id' => $this->user->id,
            'is_published' => true
        ]);

        $post2 = Post::create([
            'name' => 'Post 2',
            'slug' => 'post-2',
            'description' => 'Description 2',
            'category_id' => $category->id,
            'user_id' => $this->user->id,
            'is_published' => true
        ]);

        $this->elasticsearch->indexPost($post1);
        $this->elasticsearch->indexPost($post2);

        $category->update(['name' => 'Advanced Science']);

        $job = new \App\Blog\IO\Jobs\UpdateCategoryPostsJob($category->fresh());
        $job->handle($this->elasticsearch);

        $results = $this->elasticsearch->searchPosts([], 0, 10);

        foreach ($results['hits'] as $hit) {
            if (in_array($hit['_source']['name'], ['Post 1', 'Post 2'])) {
                $this->assertEquals('Advanced Science', $hit['_source']['category_name']);
            }
        }
    }
}
