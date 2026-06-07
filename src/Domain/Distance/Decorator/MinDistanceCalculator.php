<?php

namespace Testcenter\Domain\Distance\Decorator;

use Testcenter\Domain\Distance\DistanceCalculator;
use Testcenter\Domain\Distance\Point;

class MinDistanceCalculator implements DistanceCalculator
{
    public function __construct(
        private readonly DistanceCalculator $calculator,
        private readonly float $minimumDistance = 200,
    ) {
    }

    public function getDistance(Point $point1, Point $point2): float
    {
        $distance = $this->calculator->getDistance($point1, $point2);

        if ($distance < $this->minimumDistance) {
            return $this->minimumDistance;
        }

        return $distance;
    }
}
