<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Alarm;

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
        // Share alarm count globally for all views
        View::composer('*', function ($view) {
            $alarmCount = Alarm::where('alarm_status', '!=', 'Normal')->count();
            $view->with('alarmCount', $alarmCount);
        });
    }
}
