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
            $pdo = DB::connection()->getPdo();
            $pdo->exec(<<<SQL
                PRAGMA foreign_keys = ON;
                PRAGMA encoding = "UTF-8";
                PRAGMA journal_mode = WAL;
                PRAGMA synchronous = NORMAL;
                PRAGMA cache_size = 100000;
                PRAGMA temp_store = MEMORY;
                PRAGMA busy_timeout = 5000;
            SQL);
        }
    }
}
