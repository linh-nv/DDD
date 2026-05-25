<?php

namespace Testcenter\Domain\Question\Category;

use InvalidArgumentException;

class Categories
{
    public function __construct(
        private readonly array $names,
    ) {
        if (empty($names)) {
            throw new InvalidArgumentException('Categories cannot be empty');
        }

        if (count($names) !== count(array_unique($names))) {
            throw new InvalidArgumentException('Duplicate category detected');
        }
    }

    public function all(): array
    {
        return $this->names;
    }

    public function contains(string $name): bool
    {
        return in_array($name, $this->names, true);
    }
}
