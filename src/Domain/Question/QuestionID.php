<?php

namespace Testcenter\Domain\Question;

class QuestionID
{
    public function __construct(
        private readonly string $id
    ) {
        if (empty($id)) {
            throw new \InvalidArgumentException('Question ID cannot be empty');
        }
    }

    public static function generate(): self
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return new self(vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4)));
    }

    public function value(): string
    {
        return $this->id;
    }
}
