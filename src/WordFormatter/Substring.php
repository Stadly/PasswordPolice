<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordFormatter;

use InvalidArgumentException;
use Stadly\PasswordPolice\WordFormatter;
use Traversable;

final class Substring implements WordFormatter
{
    /**
     * @var int Minimum substring length.
     */
    private $minLength;

    /**
     * @var int|null Maximum substring length.
     */
    private $maxLength;

    /**
     * @param int $minLength Ignore substrings shorter thant this.
     * @param int|null $maxLength Ignore substrings longer than this.
     */
    public function __construct(int $minLength = 3, ?int $maxLength = 25)
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
     * {@inheritDoc}
     */
    public function apply(string $word): Traversable
    {
        for ($start = 0; $start < mb_strlen($word); ++$start) {
            $substring = mb_substr($word, $start, $this->maxLength);

            for ($length = mb_strlen($substring); $this->minLength <= $length; --$length) {
                yield mb_substr($substring, 0, $length);
            }
        }
    }
}
