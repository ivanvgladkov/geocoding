<?php

namespace Ivanvgladkov\Geocoding;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Cache\Repository as CacheContract;
use Illuminate\Contracts\Container\Container as ContainerContract;

/**
 * Class GeocodingServiceProvider
 * @package Ivanvgladkov\Geocoding
 */
class GeocodingServiceProvider extends ServiceProvider
{
    protected $defer = true;

    public function register()
    {
        $this->app->bind(Geocoding::class, function (ContainerContract $app) {
            $cache = $app->make(CacheContract::class);
            $googleApiKey = $app->make('config')->get('geocoding.google_api_key');

            $geocoding = new Geocoding($googleApiKey);
            $geocoding->setCache($cache);

            return $geocoding;
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/geocoding.php' => config_path('geocoding.php')
        ], 'config');
    }

    public function provides()
    {
        return [Geocoding::class];
    }

}