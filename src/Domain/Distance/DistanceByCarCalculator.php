<?php

namespace Testcenter\Domain\Distance;

class DistanceByCarCalculator extends AbstractDistanceCalculator
{
    protected function exec(Point $point1, Point $point2): float
    {
        // Gọi API Google Maps với phương tiện là ô tô
        return 12.0;
    }
}