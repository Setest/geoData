<?php

declare (strict_types = 1);

namespace App\Services\GeoData;

use App\Services\GeoData\GeoDataInterface;

/**
 */
abstract class GeoDataBuilder implements GeoDataInterface
{
    /**
     * Prepare result data
     *
     * @param mixed $data
     * @return array
     */
    public function prepare($data = []): array
    {
        return $data;
    }
}