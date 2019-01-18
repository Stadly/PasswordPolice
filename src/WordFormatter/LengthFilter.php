<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordFormatter;

use InvalidArgumentException;
use Traversable;

final class LengthFilter extends ChainableFormatter
{
    /**
     * @var int Minimum word length.
     */
    private $minLength;

    /**
     * @var int|null Maximum word length.
     */
    private $maxLength;

    /**
     * @param int $minLength Minimum word length.
     * @param int|null $maxLength Maximum word length.
     */
    public function __construct(int $minLength = 1, ?int $maxLength = null)
    {
        if ($minLength < 1) {
            throw new InvalidArgumentException('Min length must be positive.');
        }
        if ($maxLength !== null && $maxLength < $minLength) {
            throw new InvalidArgumentException('Max length cannot be smaller than min length.');
        }

        $this->minLength = $minLength;
        $this->maxLength = $maxLength;
    }

    /**
     * @param iterable<string> $words Words to filter.
     * @return Traversable<string> The words that match the length criteria.
     */
    protected function applyCurrent(iterable $words): Traversable
    {
        foreach ($words as $word) {
            if ($this->minLength <= mb_strlen($word) &&
               ($this->maxLength === null || mb_strlen($word) <= $this->maxLength)
            ) {
                yield $word;
            }
        }
    }
}
