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

class UpdatePostIndexJob implements ShouldQueue
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
            $result = $elasticsearch->updatePost($this->post);

            if ($result) {
                Log::info("Post updated in index successfully", ['post_id' => $this->post->id]);
            } else {
                Log::error("Failed to update post in index", ['post_id' => $this->post->id]);
            }
        } catch (\Exception $e) {
            Log::error("Exception while updating post in index: " . $e->getMessage(), [
                'post_id' => $this->post->id,
                'exception' => $e
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("UpdatePostIndexJob failed", [
            'post_id' => $this->post->id,
            'exception' => $exception->getMessage()
        ]);
    }
}
