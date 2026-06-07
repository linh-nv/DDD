<?php

namespace Testcenter\Domain\Distance;

abstract class AbstractDistanceCalculator implements DistanceCalculator
{
    public function __construct(
        protected readonly ?DistanceCalculator $fallback = null,
    ) {}

    public function getDistance(Point $point1, Point $point2): float
    {
        try {
            return $this->exec($point1, $point2);
        } catch (\Throwable $exception) {
            if ($this->fallback !== null) {
                return $this->fallback->getDistance($point1, $point2);
            }

            throw new \RuntimeException($exception->getMessage());
        }
    }

    abstract protected function exec(Point $point1, Point $point2): float;
}