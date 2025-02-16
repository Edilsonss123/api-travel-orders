<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;

class SqliteTestServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        if (config('app.env') === 'testing' && config('database.default') === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
            DB::statement('PRAGMA encoding = "UTF-8";');
            DB::statement('PRAGMA journal_mode = WAL;');
            DB::statement('PRAGMA synchronous = NORMAL;');
            DB::statement('PRAGMA cache_size = 100000;');
            DB::statement('PRAGMA temp_store = MEMORY;');
            DB::statement('PRAGMA busy_timeout = 5000;');
        }
    }
}
