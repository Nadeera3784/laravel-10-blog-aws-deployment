<?php

namespace App\Blog\IO\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Blog\IO\Services\ElasticsearchService;
use App\Blog\Entities\Post;
use Illuminate\Support\Facades\Log;

class IndexPostJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected Post $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    public function handle(ElasticsearchService $elasticsearch): void
    {
        try {
            $result = $elasticsearch->indexPost($this->post);

            if ($result) {
                Log::info("Post indexed successfully", ['post_id' => $this->post->id]);
            } else {
                Log::error("Failed to index post", ['post_id' => $this->post->id]);
            }
        } catch (\Exception $e) {
            Log::error("Exception while indexing post: " . $e->getMessage(), [
                'post_id' => $this->post->id,
                'exception' => $e
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("IndexPostJob failed", [
            'post_id' => $this->post->id,
            'exception' => $exception->getMessage()
        ]);
    }
}
