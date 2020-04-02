<?php

declare(strict_types=1);

namespace App\Services\GeoData;

/**
 * Interface GeoDataInterface
 */
interface GeoDataInterface
{
    /**
     * @param string $ip         IP address
     * @param null|string $part  if null return full data
     * @return array
     */
    public function get(string $ip, ?string $part): ?array;

    /**
     * @param string $ip IP address
     * @return array
     */
    public function getCountry(string $ip): ?array;

    /**
     * @param string $ip   IP address
     * @param string $lang language identifier
     * @return string
     */
    public function getCountryName(string $ip, string $lang = 'en'): ?string;

    /**
     * @param string $ip IP address
     * @return array
     */
    public function getRegion(string $ip): ?array;

    /**
     * @param string $ip   IP address
     * @param string $lang language identifier
     * @return string
     */
    public function getRegionName(string $ip, string $lang = 'en'): ?string;

    /**
     * @param string $ip IP address
     * @return array
     */
    public function getCity(string $ip): ?array;

    /**
     * @param string $ip   IP address
     * @param string $lang language identifier
     * @return string
     */
    public function getCityName(string $ip, string $lang = 'en'): ?string;

    /**
     * Prepare result data
     *
     * @param mixed $data
     * @return array
     */
    public function prepare($data = []): array;
}