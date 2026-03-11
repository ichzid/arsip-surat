<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use App\Models\Setting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        try {
            if (Schema::hasTable('settings')) {
                $settings = Setting::all()->pluck('value', 'key')->toArray();
                View::share('settings', $settings);
            }
        } catch (\Exception $e) {
            // Ignored during initial setup/migration
        }
    }
}
