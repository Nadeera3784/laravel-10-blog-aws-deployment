<?php

namespace App\Blog\IO\Commands;

use Illuminate\Console\Command;
use App\Blog\IO\Jobs\RecreateElasticsearchPostIndexJob;

class RecreateElasticsearchIndex extends Command
{
    protected $signature = 'elasticsearch:recreate-index {--sync : Run synchronously without job queue}';


    protected $description = 'Recreate the Elasticsearch posts index and reindex all posts';

    public function handle(): int
    {
        $this->info('Starting Elasticsearch index recreation...');

        if ($this->option('sync')) {
            $job = new RecreateElasticsearchPostIndexJob;
            $job->handle(app(\App\Blog\IO\Services\ElasticsearchService::class));
            $this->info('Elasticsearch index recreated successfully (synchronous)');
        } else {
            RecreateElasticsearchPostIndexJob::dispatch();
            $this->info('Elasticsearch index recreation job dispatched');
        }

        return Command::SUCCESS;
    }
}
