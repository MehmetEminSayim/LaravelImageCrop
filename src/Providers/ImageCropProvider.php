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
    }
}
