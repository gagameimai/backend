<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * 使用function
     *
     * @var array
     */
    protected $helpserMap = [
        'ToolsHelper',
        'ValidatorHelper',
        'SqlHelper',
    ];

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        foreach($this->helpserMap as $help) {
            $path = app_path(). '/Helpers/' . $help . '.php';
            if(\File::isFile($path)){
                require_once $path;
            }
        }
    }
}
