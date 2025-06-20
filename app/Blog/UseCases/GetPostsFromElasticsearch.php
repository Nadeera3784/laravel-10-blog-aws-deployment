<?php

namespace App\Blog\UseCases;

use App\Blog\IO\Services\ElasticsearchService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class GetPostsFromElasticsearch
{
    private ElasticsearchService $elasticsearch;

    public function __construct(ElasticsearchService $elasticsearch)
    {
        $this->elasticsearch = $elasticsearch;
    }

    public function execute(?int $categoryId = null, ?string $search = null, int $page = 1, int $perPage = 9): LengthAwarePaginator
    {
        $from = ($page - 1) * $perPage;

        $filters = [];
        if ($categoryId) {
            $filters['category_id'] = $categoryId;
        }
        if ($search) {
            $filters['search'] = $search;
        }

        $results = $this->elasticsearch->searchPosts($filters, $from, $perPage);

        $posts = collect($results['hits'])->map(function ($hit) {
            $source = $hit['_source'];

            return (object) [
                'id' => $source['id'],
                'name' => $source['name'],
                'slug' => $source['slug'],
                'description' => $source['description'],
                'category_id' => $source['category_id'],
                'category_name' => $source['category_name'] ?? null,
                'user_id' => $source['user_id'],
                'user_name' => $source['user_name'] ?? null,
                'is_published' => $source['is_published'],
                'image' => $source['image'],
                'image_url' => $source['image'] ? Storage::url($source['image']) : null,
                'created_at' => Carbon::parse($source['created_at']),
                'updated_at' => Carbon::parse($source['updated_at']),
                'category' => $source['category_name'] ? (object) [
                    'id' => $source['category_id'],
                    'name' => $source['category_name']
                ] : null,
                'user' => $source['user_name'] ? (object) [
                    'id' => $source['user_id'],
                    'name' => $source['user_name']
                ] : null,
            ];
        });

        $paginator = new LengthAwarePaginator(
            $posts,
            $results['total'],
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'pageName' => 'page',
            ]
        );

        $paginator->appends(request()->query());

        return $paginator;
    }
}
