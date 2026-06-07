<?php

namespace Testcenter\Application\Distance;

class CalculateDistanceCommand
{
    public function __construct(
        public readonly float $fromLat,
        public readonly float $fromLong,
        public readonly float $toLat,
        public readonly float $toLong,
        public readonly string $method,
        public readonly bool $useCache,
        public readonly bool $useMinimumDistance,
        public readonly float $minimumDistance,
    ) {
    }
}
