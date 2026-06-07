<?php

namespace Testcenter\Domain\Distance;

use Testcenter\Domain\Distance\Exception\UnsupportedDistanceMethodException;

class DistanceCalculatorFactory
{
    /**
     * @param string $method
     * @return DistanceCalculator
     * @throws UnsupportedDistanceMethodException
     */
    public function make(string $method): DistanceCalculator
    {
        return match ($method) {
            // mac dinh se la chim bay
            'auto' => new BirdFlyDistanceCalculator(),
            'car' => new DistanceByCarCalculator(
                new DistanceByBikeCalculator(
                    new BirdFlyDistanceCalculator()
                )
            ),
            'bike' => new DistanceByBikeCalculator(
                new BirdFlyDistanceCalculator()
            ),
            'bird_fly' => new BirdFlyDistanceCalculator(),
            default => throw new UnsupportedDistanceMethodException("Khong ho tro phuong thuc tinh khoang cach nay"),
        };
    }
}
