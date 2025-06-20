<?php

namespace App\Blog\IO\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Blog\IO\Services\ElasticsearchService;
use App\Blog\Entities\Category;
use App\Blog\Entities\Post;
use Illuminate\Support\Facades\Log;

class UpdateCategoryPostsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected Category $category;

    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    public function handle(ElasticsearchService $elasticsearch): void
    {
        try {
            $posts = Post::with(['category', 'user'])
                ->where('category_id', $this->category->id)
                ->get();

            if ($posts->count() > 0) {
                /** @var Post $post */
                foreach ($posts as $post) {
                    $result = $elasticsearch->updatePost($post);

                    if (!$result) {
                        Log::warning("Failed to update post in Elasticsearch after category update", [
                            'post_id' => $post->id,
                            'category_id' => $this->category->id,
                            'category_name' => $this->category->name
                        ]);
                    }
                }

                Log::info("Updated posts in Elasticsearch after category update", [
                    'category_id' => $this->category->id,
                    'category_name' => $this->category->name,
                    'posts_updated' => $posts->count()
                ]);
            } else {
                Log::info("No posts found for category update", [
                    'category_id' => $this->category->id,
                    'category_name' => $this->category->name
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Exception while updating category posts in Elasticsearch: " . $e->getMessage(), [
                'category_id' => $this->category->id,
                'category_name' => $this->category->name,
                'exception' => $e
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("UpdateCategoryPostsJob failed", [
            'category_id' => $this->category->id,
            'category_name' => $this->category->name,
            'exception' => $exception->getMessage()
        ]);
    }
}
