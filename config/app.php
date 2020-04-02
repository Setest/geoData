<?php

return [

    /* common config */

    /* ... */

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => [

        /*
         * Laravel Framework Service Providers...
         */

        /* ... */

        /*
        * Application Additional Providers...
        */
        App\Providers\GeoDataServiceProvider::class,
        Chumper\Zipper\ZipperServiceProvider::class
    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => [
        /* ... */
        'GeoData' => App\Providers\GeoDataServiceProvider::class,
        'Zipper' => Chumper\Zipper\Zipper::class
    ]
];
