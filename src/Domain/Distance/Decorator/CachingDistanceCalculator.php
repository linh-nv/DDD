<?php

namespace Testcenter\Domain\Distance\Decorator;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Testcenter\Domain\Distance\DistanceCalculator;
use Testcenter\Domain\Distance\Point;

class CachingDistanceCalculator implements DistanceCalculator
{
    public function __construct(
        private readonly DistanceCalculator $calculator,
        private readonly string $method,
    ) {
    }

    public function getDistance(Point $point1, Point $point2): float
    {
        $keyCache = $this->getKeyCache($point1, $point2);
        $distance = Cache::get($keyCache);

        if ($distance !== null) {
            return (float) $distance;
        }

        $distance = $this->calculator->getDistance($point1, $point2);
        Cache::put($keyCache, $distance, Carbon::now()->addWeek());

        return $distance;
    }

    /**
     * format: method:lat1-long1:lat2-long2
     * @param Point $point1
     * @param Point $point2
     * @return string
     */
    private function getKeyCache(Point $point1, Point $point2): string
    {
        return $this->method . ':' . $point1->getLat() . '-' . $point1->getLong() . ':' . $point2->getLat() . '-' . $point2->getLong();
    }
}
