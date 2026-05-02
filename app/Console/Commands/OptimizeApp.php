<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class OptimizeApp extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:optimize {--clear : Clear all caches first}';

    /**
     * The console command description.
     */
    protected $description = 'Optimize the application for production';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🚀 Optimizing application...');

        if ($this->option('clear')) {
            $this->info('Clearing all caches...');
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            Artisan::call('event:clear');
            $this->info('✓ All caches cleared');
        }

        // Cache configuration
        $this->info('Caching configuration...');
        Artisan::call('config:cache');
        $this->info('✓ Configuration cached');

        // Cache routes
        $this->info('Caching routes...');
        Artisan::call('route:cache');
        $this->info('✓ Routes cached');

        // Cache views
        $this->info('Caching Blade views...');
        Artisan::call('view:cache');
        $this->info('✓ Views cached');

        // Cache events
        $this->info('Caching events...');
        Artisan::call('event:cache');
        $this->info('✓ Events cached');

        // Clear page cache
        $this->info('Clearing page cache...');
        $this->clearPageCache();
        $this->info('✓ Page cache cleared');

        $this->newLine();
        $this->info('✅ Application optimized successfully!');

        return Command::SUCCESS;
    }

    /**
     * Clear all page cache entries
     */
    protected function clearPageCache(): void
    {
        // Clear all keys with page_cache prefix
        Cache::flush(); // In production, use tagged cache or specific keys
    }
}
