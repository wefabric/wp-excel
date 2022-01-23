<?php

namespace Wefabric\WPExcel\Providers;


use Wefabric\WPExcel\Hooks\BulkActions;
use Wefabric\WPExcel\WpPostsExcelExport;
use Wefabric\WPSupport\WPSupport;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register()
    {
        parent::register();

        // Hooks
        WPSupport::getInstance()->addHooks([
            BulkActions::class,
        ]);

        // Register export classes
        if($postTypes = config('wp-excel.post_types')) {
            WpPostsExcelExport::register($postTypes);
        }

    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/wp-excel.php' => config_path('wp-excel.php'),
        ], ['wp-excel', 'config']);
    }

}