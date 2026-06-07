<?php

namespace Testcenter\Application\Distance;

use Testcenter\Domain\Distance\Decorator\CachingDistanceCalculator;
use Testcenter\Domain\Distance\Decorator\MinDistanceCalculator;
use Testcenter\Domain\Distance\DistanceCalculator;
use Testcenter\Domain\Distance\DistanceCalculatorFactory;
use Testcenter\Domain\Distance\Point;

class CalculateDistanceHandler
{
    public function __construct(
        private readonly DistanceCalculatorFactory $calculatorFactory,
    ) {
    }

    public function handle(CalculateDistanceCommand $command)
    {
        $point1 = new Point($command->fromLat, $command->fromLong);
        $point2 = new Point($command->toLat, $command->toLong);
        $calculator = $this->buildCalculator($command);

        return [
            'method' => $command->method,
            'use_cache' => $command->useCache,
            'use_minimum_distance' => $command->useMinimumDistance,
            'minimum_distance' => $command->useMinimumDistance ? $command->minimumDistance : null,
            'distance' => $calculator->getDistance($point1, $point2),
        ];
    }

    private function buildCalculator(CalculateDistanceCommand $command): DistanceCalculator
    {
        $calculator = $this->calculatorFactory->make($command->method);

        if ($command->useCache) {
            $calculator = new CachingDistanceCalculator($calculator, $command->method);
        }

        if ($command->useMinimumDistance) {
            $calculator = new MinDistanceCalculator(
                calculator: $calculator,
                minimumDistance: $command->minimumDistance,
            );
        }

        return $calculator;
    }
}
