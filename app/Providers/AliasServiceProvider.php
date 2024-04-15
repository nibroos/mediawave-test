<?php

namespace App\Providers;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class AliasServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $loader = AliasLoader::getInstance();

        // $loader->alias('Setting', \App\Facades\Setting::class);
        // $loader->alias('HelperMethods', \App\Facades\HelperMethods::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
