<?php

namespace App\Blog\IO\Services;

use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Client;
use App\Blog\Entities\Post;
use Illuminate\Support\Facades\Log;

class ElasticsearchService
{
    private Client $client;
    private string $indexName;

    public function __construct()
    {
        $this->client = ClientBuilder::create()
            ->setHosts([config('elasticsearch.host', 'elasticsearch:9200')])
            ->setBasicAuthentication(config('elasticsearch.username'), config('elasticsearch.password'))
            ->build();

        $this->indexName = config('elasticsearch.index', 'blog_posts');
    }

    public function createIndex(): bool
    {
        try {
            if ($this->indexExists()) {
                $this->deleteIndex();
            }

            $params = [
                'index' => $this->indexName,
                'body' => config('elasticsearch-post-index-template.body')
            ];

            $response = $this->client->indices()->create($params);
            return $response['acknowledged'] ?? false;
        } catch (\Exception $e) {
            Log::error('Elasticsearch create index error: ' . $e->getMessage());
            return false;
        }
    }

    public function deleteIndex(): bool
    {
        try {
            if (!$this->indexExists()) {
                return true;
            }

            $params = ['index' => $this->indexName];
            $response = $this->client->indices()->delete($params);
            return $response['acknowledged'] ?? false;
        } catch (\Exception $e) {
            Log::error('Elasticsearch delete index error: ' . $e->getMessage());
            return false;
        }
    }

    public function indexExists(): bool
    {
        try {
            $params = ['index' => $this->indexName];
            $response = $this->client->indices()->exists($params);
            return $response->getStatusCode() === 200;
        } catch (\Exception $e) {
            Log::error('Elasticsearch index exists check error: ' . $e->getMessage());
            return false;
        }
    }

    public function refreshIndex(): bool
    {
        try {
            $params = ['index' => $this->indexName];
            $response = $this->client->indices()->refresh($params);
            return true;
        } catch (\Exception $e) {
            Log::error('Elasticsearch refresh index error: ' . $e->getMessage());
            return false;
        }
    }

    public function indexPost(Post $post): bool
    {
        try {
            $params = [
                'index' => $this->indexName,
                'id' => $post->id,
                'body' => $this->preparePostData($post)
            ];

            $response = $this->client->index($params);
            return isset($response['result']) && in_array($response['result'], ['created', 'updated']);
        } catch (\Exception $e) {
            Log::error('Elasticsearch index post error: ' . $e->getMessage());
            return false;
        }
    }

    public function updatePost(Post $post): bool
    {
        try {
            $params = [
                'index' => $this->indexName,
                'id' => $post->id,
                'body' => [
                    'doc' => $this->preparePostData($post),
                    'doc_as_upsert' => true
                ]
            ];

            $response = $this->client->update($params);
            return isset($response['result']) && in_array($response['result'], ['updated', 'created', 'noop']);
        } catch (\Exception $e) {
            Log::error('Elasticsearch update post error: ' . $e->getMessage());
            return false;
        }
    }

    public function deletePost(int $postId): bool
    {
        try {
            $params = [
                'index' => $this->indexName,
                'id' => $postId
            ];

            $response = $this->client->delete($params);
            return isset($response['result']) && $response['result'] === 'deleted';
        } catch (\Exception $e) {
            Log::error('Elasticsearch delete post error: ' . $e->getMessage());
            return false;
        }
    }

    public function bulkIndexPosts($posts): bool
    {
        try {
            $params = ['body' => []];

            foreach ($posts as $post) {
                $params['body'][] = [
                    'index' => [
                        '_index' => $this->indexName,
                        '_id' => $post->id
                    ]
                ];
                $params['body'][] = $this->preparePostData($post);
            }

            if (!empty($params['body'])) {
                $response = $this->client->bulk($params);
                return !($response['errors'] ?? false);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Elasticsearch bulk index error: ' . $e->getMessage());
            return false;
        }
    }

    public function searchPosts(array $filters = [], int $from = 0, int $size = 9): array
    {
        try {
            $body = [
                'query' => [
                    'bool' => [
                        'must' => []
                    ]
                ],
                'sort' => [
                    ['created_at' => ['order' => 'desc']]
                ],
                'from' => $from,
                'size' => $size
            ];

            $body['query']['bool']['must'][] = [
                'term' => ['is_published' => true]
            ];

            if (!empty($filters['category_id'])) {
                $body['query']['bool']['must'][] = [
                    'term' => ['category_id' => $filters['category_id']]
                ];
            }

            if (!empty($filters['search'])) {
                $body['query']['bool']['must'][] = [
                    'multi_match' => [
                        'query' => $filters['search'],
                        'fields' => ['name^2', 'description', 'category_name']
                    ]
                ];
            }

            $params = [
                'index' => $this->indexName,
                'body' => $body
            ];

            $response = $this->client->search($params);

            return [
                'hits' => $response['hits']['hits'] ?? [],
                'total' => $response['hits']['total']['value'] ?? 0
            ];
        } catch (\Exception $e) {
            Log::error('Elasticsearch search error: ' . $e->getMessage());
            return [
                'hits' => [],
                'total' => 0
            ];
        }
    }

    private function preparePostData(Post $post): array
    {
        $post->load(['category', 'user']);

        return [
            'id' => $post->id,
            'name' => $post->name,
            'slug' => $post->slug,
            'description' => $post->description,
            'category_id' => $post->category_id,
            'category_name' => $post->category?->name,
            'user_id' => $post->user_id,
            'user_name' => $post->user?->name,
            'is_published' => $post->is_published,
            'image' => $post->image,
            'created_at' => $post->created_at?->toISOString(),
            'updated_at' => $post->updated_at?->toISOString(),
        ];
    }
}
