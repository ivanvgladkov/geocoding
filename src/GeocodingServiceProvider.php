<?php

namespace Ivanvgladkov\Geocoding;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Cache\Repository as CacheContract;

class GeocodingServiceProvider extends ServiceProvider
{
    protected $defer = true;

    public function register()
    {
        //
    }

    public function boot(CacheContract $cache)
    {
        $this->app->bind(Geocoding::class, function ($app) use ($cache) {
            $geocoding = new Geocoding($app['config']['app']['google']['api_key']);
            $geocoding->setCache($cache);
            return $geocoding;
        });
    }

    public function provides()
    {
        return [Geocoding::class];
    }

}