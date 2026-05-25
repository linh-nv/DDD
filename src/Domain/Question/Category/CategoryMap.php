<?php

namespace Testcenter\Domain\Question\Category;

use InvalidArgumentException;

class CategoryMap
{
    /**
     * @param array<string, string> $assignments item => category
     */
    public function __construct(
        private readonly array $assignments,
        private readonly Categories $categories,
    ) {
        if (empty($assignments)) {
            throw new InvalidArgumentException('Category map cannot be empty');
        }

        foreach ($assignments as $category) {
            if (!$this->categories->contains($category)) {
                throw new InvalidArgumentException("Unknown category: {$category}");
            }
        }
    }

    public function isCorrect(string $item, string $category): bool
    {
        return isset($this->assignments[$item]) && $this->assignments[$item] === $category;
    }

    public function total(): int
    {
        return count($this->assignments);
    }

    public function all(): array
    {
        return $this->assignments;
    }
}
