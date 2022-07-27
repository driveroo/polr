<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $rootUrl = env('APP_PROTOCOL') . env('APP_ADDRESS');

        $url = $this->app['url'];

        $reflectionClass = new \ReflectionClass($url);

        $property = $reflectionClass->getProperty('cachedRoot');
        $property->setAccessible(true);
        $property->setValue($url, $rootUrl);

        \Illuminate\Support\Facades\URL::forceSchema('https');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        \Illuminate\Support\Facades\URL::forceSchema('https');
    }
}
