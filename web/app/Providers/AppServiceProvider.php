<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        if (str_starts_with(config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }
        Carbon::setLocale(config('app.locale'));

        View::composer(['site.*', 'components.site.*'], fn ($view) => $view->with('site', Setting::allValues()));
    }
}
