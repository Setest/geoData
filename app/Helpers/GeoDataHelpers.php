<?php
namespace App\Helpers;

use App\Services\GeoData\GeoData;
use App\Exceptions\GeoDataException;
use Illuminate\Support\Facades\Log;

/**
 * GeoDataHelpers.
 *
 * Wrap for GeoData service.
 */
class GeoDataHelpers
{
    private static $instances = [];

    /**
     * __construct.
     *
     * Protect method from extending.
     *
     * @return void
     */
    protected function __construct()
    {
    }

    /**
     * __clone.
     *
     * Protect method from extending.
     *
     * @return void
     */
    protected function __clone()
    {
    }

    /**
     * __wakeup.
     *
     * Protect method from extending.
     *
     * @return void
     */
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton.");
    }

    /**
     * getInstance.
     *
     * Return singleton object.
     *
     * @return GeoData
     */
    public static function getInstance()
    {
        $cls = static::class;
        if (!isset(static::$instances[$cls])) {
            $provider = config('geodata.provider');
            // FIXME можно не использовать switch, а использовать получение имени класса из конфига
            switch ($provider) {
                case 'sypexgeo':
                    $className = 'SypexGeo';
                    break;

                default:
                    throw new GeoDataException("Service GeoData: provider '${provider}' is not exist!");
                    break;
            }

            $config = config('geodata.' . $provider);

            $class = new \ReflectionClass('App\Services\GeoData\\' . $className);
            static::$instances[$cls] = $class->newInstance($config);
        }

        return static::$instances[$cls];
    }

    /**
     * __callStatic.
     *
     * Dynamically event GeoData methods which exist and have public access.
     *
     * @return GeoData
     */
    public static function __callStatic($name, $arguments)
    {
        $geoData = static::getInstance();

        if ($name && !method_exists($geoData, $name)) {
            throw new GeoDataException("Method '$name' not exist in " . get_class($geoData));
        }

        $handler = new \ReflectionMethod($geoData, $name);
        // $handler->setAccessible(true); // enable access for private methods
        return $handler->invokeArgs($geoData, $arguments);
    }
}