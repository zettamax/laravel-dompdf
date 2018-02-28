<?php
namespace Barryvdh\DomPDF;

use Dompdf\Dompdf;
use Exception;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

class ServiceProvider extends IlluminateServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @throws \Exception
     * @return void
     */
    public function register()
    {
        $this->app['config']->package('barryvdh/laravel-dompdf', __DIR__ . '/config');

        $this->app->bind('dompdf.options', function($app){
            $defines = $app['config']->get('laravel-dompdf::defines');
            if ($defines) {
                $options = [];
                foreach ($defines as $key => $value) {
                    $key = strtolower(str_replace('DOMPDF_', '', $key));
                    $options[$key] = $value;
                }
            } else {
                $options = $app['config']->get('laravel-dompdf::options');
            }
            return $options;
        });

        $this->app->bind('dompdf', function($app) {
            $options = $app->make('dompdf.options');
            $dompdf = new Dompdf($options);
            $dompdf->setBasePath(realpath(base_path('public')));
            return $dompdf;
        });

        $this->app->bind('dompdf.wrapper', function ($app) {
                return new PDF($app['dompdf'], $app['config'], $app['files'], $app['view']);
            });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('dompdf', 'dompdf.wrapper', 'dompdf.options');
    }

}
