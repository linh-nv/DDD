<?php

namespace Testcenter\Domain\Distance;

class BirdFlyDistanceCalculator extends AbstractDistanceCalculator
{
    protected function exec(Point $point1, Point $point2): float
    {
        $earthRadius = 6371; // Earth radius in kilometers

        $latFrom = deg2rad($point1->lat());
        $lonFrom = deg2rad($point1->long());
        $latTo = deg2rad($point2->lat());
        $lonTo = deg2rad($point2->long());

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $angle * $earthRadius;
    }
}