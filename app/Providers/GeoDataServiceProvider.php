<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class GeoDataServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     * @throws GeoDataException
     */
    public function register(...$args): void
    {
        require_once app_path() . '/Helpers/GeoDataHelpers.php';
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
