<?php

namespace UrlShortener\Providers;

use Illuminate\Support\ServiceProvider;
use UrlShortener\Observers\ShortenerObserver;
use UrlShortener\Shortener;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Shortener::observe(ShortenerObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
