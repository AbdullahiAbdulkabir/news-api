<?php

namespace App\Console\Commands;

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
    public function handle()
    {
        $this->info('Fetching News Feed...');
//        @todo add action here
        $this->info('News fetched Successful');
    }
}
