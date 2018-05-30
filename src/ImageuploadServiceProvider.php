<?php

namespace Pelima\Imageupload;

use Illuminate\Support\ServiceProvider;
use Intervention\Image\ImageManager;

class ImageuploadServiceProvider extends ServiceProvider
{
    protected $defer = false;

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish config file
        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('imageupload.php'),
        ], 'config-imageupload');

        // Publish migration file
        $this->publishes([
            __DIR__.'/../database/migrations/2018_05_20_154429_create_file_uploads_table.php' => database_path('migrations/2018_05_20_154429_create_file_uploads_table.php'),
        ], 'migrations-imageupload');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'imageupload');

        $this->app->singleton('imageupload', function ($app) {
            return new Imageupload(new ImageManager());
        });
    }
}
