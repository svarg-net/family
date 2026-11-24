<?php

declare(strict_types=1);

namespace App\Domain\Task\ValueObjects;

use InvalidArgumentException;
use Stringable;

final readonly class TaskTitle implements Stringable
{
    private const MAX_LENGTH = 255;

    public string $value;

    public function __construct(string $value)
    {
        $trimmed = trim($value);

        if ($trimmed === '') {
            throw new InvalidArgumentException('Заголовок задачи не может быть пустым');
        }

        if (mb_strlen($trimmed) > self::MAX_LENGTH) {
            throw new InvalidArgumentException(
                sprintf('Заголовок задачи не может быть длиннее %d символов', self::MAX_LENGTH)
            );
        }

        $this->value = $trimmed;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
