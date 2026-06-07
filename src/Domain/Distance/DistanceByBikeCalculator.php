<?php

namespace Testcenter\Domain\Distance;

class DistanceByBikeCalculator extends AbstractDistanceCalculator
{
    protected function exec(Point $point1, Point $point2): float
    {
        // Gọi API Google Maps với phương tiện là xe đạp
        return 10.0;
    }
}