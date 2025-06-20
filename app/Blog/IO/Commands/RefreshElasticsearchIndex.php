<?php

namespace App\Blog\IO\Commands;

use Illuminate\Console\Command;
use App\Blog\IO\Services\ElasticsearchService;

class RefreshElasticsearchIndex extends Command
{
    protected $signature = 'elasticsearch:refresh-index';


    protected $description = 'Refresh the Elasticsearch posts index';


    public function handle(ElasticsearchService $elasticsearch): int
    {
        $this->info('Refreshing Elasticsearch index...');

        try {
            $result = $elasticsearch->refreshIndex();

            if ($result) {
                $this->info('Elasticsearch index refreshed successfully');
                return Command::SUCCESS;
            } else {
                $this->error('Failed to refresh Elasticsearch index');
                return Command::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error('Exception while refreshing Elasticsearch index: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
