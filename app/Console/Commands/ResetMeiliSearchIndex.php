<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\NewsTopic;
use Illuminate\Console\Command;
use MeiliSearch\Client;

class ResetMeiliSearchIndex extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset:meilisearch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset meilisearch by sorting certain indexes';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
        $client = new Client(config('scout.meilisearch.host'), config('scout.meilisearch.key'));
        $index = (new NewsTopic())->searchableAs();
        $importance = [
            'desc(id)',
            'typo',
            'words',
            'proximity',
            'attribute',
            'wordsPosition',
            'exactness',
        ];
        $client->index($index)->updateSortableAttributes(['id']);
        $client->index($index)->updateRankingRules($importance);
        $client->index($index)->updateFilterableAttributes(['__soft_deleted']);

        $index = (new Article())->searchableAs();
        $client->index($index)->updateSortableAttributes(['id']);
        $client->index($index)->updateRankingRules($importance);
        $client->index($index)->updateFilterableAttributes(['__soft_deleted']);

        $this->info('Reset the search');
    }
}
