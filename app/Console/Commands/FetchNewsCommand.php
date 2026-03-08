<?php

namespace App\Console\Commands;

use App\Services\NewsSources;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

class FetchNewsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:news {--source= : Fetch from a specific source}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch News Feed from API sources';

    /**
     * Execute the console command.
     */
    public function handle(NewsSources $newsSources): int
    {
        $specificSource = $this->option('source');
        $availableSources = $newsSources->sources;

        if ($specificSource) {
            $availableSources = $availableSources->filter(fn ($source) => $source->__toString() === $specificSource);

            if ($availableSources->isEmpty()) {
                $this->error("{$specificSource} not available");

                return CommandAlias::FAILURE;
            }
        }

        $this->info("Fetching News Feed for sources available: {$availableSources->count()}...");
        $newsSources->sync($specificSource);
        $this->info('News fetched Successful');

        return CommandAlias::SUCCESS;
    }
}
