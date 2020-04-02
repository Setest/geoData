<?php

declare(strict_types=1);

namespace App\Services\GeoData;

use App\Services\GeoData\SypexGeo as Sx;
use Illuminate\Support\Facades\Cache;

class SypexGeo extends GeoData
{
    /**
     * Default config
     *
     * @var array
     */
    private $config = [];

    /**
     * Initialized status
     *
     * @var bool
     */
    private $initialized = false;

    /**
     * Cache prefix
     *
     * @var string
     */
    private $cachePrefix = 'geoData_sx_';

    /**
     * Sypex Geo object
     *
     * @var Sx\SypexGeo
     */
    private $sx;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Initialize
     *
     * @return boolean
     */
    private function initialize(): bool
    {
        if ($this->initialized) {
            return true;
        }

        $sypexConfig = $this->config['config'];

        $sypexConfigType = $sypexConfig['type'] ?? '';
        $sypexConfigPath = $sypexConfig['path'] ?? '';
        $sypexConfigFile = $sypexConfig['file'] ?? '';

        switch ($sypexConfigType) {
            case 'database':
                $sxgeo = new Sx\SxGeo(base_path() . $sypexConfigPath . $sypexConfigFile);
                break;

            case 'web_service':
                $license_key = $sypexConfig['license_key'] ?? '';
                $sxgeo = new Sx\SxGeoHttp($license_key);
                break;

            default:
                $sxgeo = new Sx\SxGeo(base_path() . $sypexConfigPath . $sypexConfigFile);
        }

        $this->sx = new Sx\SypexGeo($sxgeo, $this->config);
        $this->initialized = true;
        return true;
    }

    /**
     * Get full data by IP
     *
     * @param string $ip
     * @param string|null $part
     * @return array|null
     */
    public function get(string $ip, ?string $part = null): ?array
    {
        $that = $this;
        $cacheName = $this->cachePrefix . $ip;
        $result = Cache::remember($cacheName, 10080, function () use ($ip, $that) {
            // $result = Cache::remember($cacheName, 0, function () use ($ip, $that) {
            $data = [];
            if ($that->initialize()) {
                $data = $that->sx->get($ip);
            }
            return $data;
        });
        return ($part) ? $result[$part] : $result;
    }

    /**
     * Get country data by IP
     *
     * @param string $ip
     * @return array|null
     */
    public function getCountry(string $ip): ?array
    {
        return $this->get($ip, 'country');
    }

    /**
     * Get country name by IP
     *
     * @param string $ip
     * @param string $lang
     * @return string|null
     */
    public function getCountryName(string $ip, string $lang = 'en'): ?string
    {
        $data = $this->getCountry($ip);
        return ($data && $result = $data["name_{$lang}"]) ? $result : null;
    }

    /**
     * Get region data by IP
     *
     * @param string $ip
     * @return array|null
     */
    public function getRegion(string $ip): ?array
    {
        return $this->get($ip, 'region');
    }

    /**
     * Get region name by IP
     *
     * @param string $ip
     * @param string $lang
     * @return string|null
     */
    public function getRegionName(string $ip, string $lang = 'en'): ?string
    {
        $data = $this->getRegion($ip);
        return ($data && $result = $data["name_{$lang}"]) ? $result : null;
    }

    /**
     * Get city data by IP
     *
     * @param string $ip
     * @return array|null
     */
    public function getCity(string $ip): ?array
    {
        return $this->get($ip, 'city');
    }

    /**
     * Get city name by IP
     *
     * @param string $ip
     * @param string $lang
     * @return string|null
     */
    public function getCityName(string $ip, string $lang = 'en'): ?string
    {
        $data = $this->getCity($ip);
        return ($data && $result = $data["name_{$lang}"]) ? $result : null;
    }
}