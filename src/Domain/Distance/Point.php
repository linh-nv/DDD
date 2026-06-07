<?php

namespace Testcenter\Domain\Distance;

use InvalidArgumentException;

class Point
{
    public function __construct(
        private readonly float $lat,
        private readonly float $long,
    ) {
        if ($lat < -90 || $lat > 90) {
            throw new InvalidArgumentException('lat must be between -90 and 90.');
        }

        if ($long < -180 || $long > 180) {
            throw new InvalidArgumentException('long must be between -180 and 180.');
        }
    }

    public function lat(): float
    {
        return $this->lat;
    }

    public function getLat(): float
    {
        return $this->lat();
    }

    public function long(): float
    {
        return $this->long;
    }

    public function getLong(): float
    {
        return $this->long();
    }

    public function toArray(): array
    {
        return [
            'lat' => $this->lat,
            'lng' => $this->long,
        ];
    }
}
