<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class GlobalFunctionsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        require_once base_path() . '/app/Functions/GlobalFunctions.php';
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
