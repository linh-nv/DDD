<?php

namespace Testcenter\Domain\Distance;

interface DistanceCalculator
{
    public function getDistance(Point $point1, Point $point2): float;
}
