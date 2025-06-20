<?php

namespace App\Blog\IO\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Blog\IO\Services\ElasticsearchService;
use Illuminate\Support\Facades\Log;

class DeletePostIndexJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected int $postId;

    public function __construct(int $postId)
    {
        $this->postId = $postId;
    }

    public function handle(ElasticsearchService $elasticsearch): void
    {
        try {
            $result = $elasticsearch->deletePost($this->postId);

            if ($result) {
                Log::info("Post deleted from index successfully", ['post_id' => $this->postId]);
            } else {
                Log::error("Failed to delete post from index", ['post_id' => $this->postId]);
            }
        } catch (\Exception $e) {
            Log::error("Exception while deleting post from index: " . $e->getMessage(), [
                'post_id' => $this->postId,
                'exception' => $e
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("DeletePostIndexJob failed", [
            'post_id' => $this->postId,
            'exception' => $exception->getMessage()
        ]);
    }
}
