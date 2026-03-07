<?php

namespace App\Console\Commands;

use App\Services\NewsSources;
use Illuminate\Console\Command;

class FetchNewsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:news';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch News Feed from API sources';

    /**
     * Execute the console command.
     */
    public function handle(NewsSources $source): int
    {
        $this->info('Fetching News Feed...');
        $source->sync();
        $this->info('News fetched Successful');

        return 0;
    }
}
