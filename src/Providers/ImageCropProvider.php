<?php

namespace Imagecrop\Mehmeteminsayim\Providers;

use Illuminate\Support\ServiceProvider;

class ImageCropProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../views', 'imagecrop');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->publishes([
            __DIR__.'/../views' => resource_path('views/vendor/imagecrop'),
        ],'imagecrop-view');
        $this->publishes([
            __DIR__.'/../database/migrations/2014_10_12_000000_create_imagecrop_table.php' =>
                $this->app->databasePath("database/migrations".now()->format('Y_m_d_His').'create_package_table.php'),
        ],'imagecrop-migrations');
    }
}
