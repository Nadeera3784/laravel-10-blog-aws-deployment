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

class RecreateElasticsearchPostIndexJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct()
    {
    }

    public function handle(ElasticsearchService $elasticsearch): void
    {
        try {
            Log::info('Starting Elasticsearch index recreation');

            $result = $elasticsearch->createIndex();

            if (!$result) {
                Log::error('Failed to create Elasticsearch index');
                return;
            }

            Log::info('Elasticsearch index recreated successfully');

            $posts = Post::with(['category', 'user'])->get();

            if ($posts->count() > 0) {
                $bulkResult = $elasticsearch->bulkIndexPosts($posts);

                if ($bulkResult) {
                    Log::info('All posts reindexed successfully', ['count' => $posts->count()]);
                } else {
                    Log::error('Failed to bulk index posts');
                }
            } else {
                Log::info('No posts to reindex');
            }

        } catch (\Exception $e) {
            Log::error('Exception while recreating Elasticsearch index: ' . $e->getMessage(), [
                'exception' => $e
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("RecreateElasticsearchPostIndexJob failed", [
            'exception' => $exception->getMessage()
        ]);
    }
}
