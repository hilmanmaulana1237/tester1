<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClearExpiredCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:clear-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear expired cache entries from database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Clearing expired cache entries...');

        // Clear expired cache dari database
        $deleted = DB::table('cache')
            ->where('expiration', '<', now()->timestamp)
            ->delete();

        $this->info("Deleted {$deleted} expired cache entries.");

        // Clear expired cache locks
        $deletedLocks = DB::table('cache_locks')
            ->where('expiration', '<', now()->timestamp)
            ->delete();

        $this->info("Deleted {$deletedLocks} expired cache locks.");

        $this->info('Cache cleanup completed successfully!');

        return Command::SUCCESS;
    }
}
