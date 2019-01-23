<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordFormatter;

use InvalidArgumentException;
use Stadly\PasswordPolice\WordFormatter;
use Traversable;

final class SubstringGenerator implements WordFormatter
{
    use FormatterChaining;

    /**
     * @var int Minimum substring length.
     */
    private $minLength;

    /**
     * @var int|null Maximum substring length.
     */
    private $maxLength;

    /**
     * @var bool Whether the result should only contain unique words.
     */
    private $filterUnique;

    /**
     * @param int $minLength Ignore substrings shorter thant this.
     * @param int|null $maxLength Ignore substrings longer than this.
     * @param bool $filterUnique Whether the result should only contain unique words.
     */
    public function __construct(int $minLength = 3, ?int $maxLength = 25, bool $filterUnique = true)
    {
        if ($minLength < 1) {
            throw new InvalidArgumentException('Min length must be positive.');
        }
        if ($maxLength !== null && $maxLength < $minLength) {
            throw new InvalidArgumentException('Max length cannot be smaller than min length.');
        }

        $this->minLength = $minLength;
        $this->maxLength = $maxLength;
        $this->filterUnique = $filterUnique;
    }

    /**
     * @param iterable<string> $words Words to format.
     * @return Traversable<string> All substrings of the words.
     */
    protected function applyCurrent(iterable $words): Traversable
    {
        if ($this->filterUnique) {
            yield from $this->formatWordsUnique($words);
        } else {
            yield from $this->formatWords($words);
        }
    }

    /**
     * @param iterable<string> $words Words to format.
     * @return Traversable<string> All substrings of the words without duplicates.
     */
    private function formatWordsUnique(iterable $words): Traversable
    {
        $unique = [];
        foreach ($words as $word) {
            for ($start = 0; $start < mb_strlen($word); ++$start) {
                $substring = mb_substr($word, $start, $this->maxLength);

                if (isset($unique[$substring])) {
                    break;
                }

                for ($length = mb_strlen($substring); $this->minLength <= $length; --$length) {
                    $substring = mb_substr($substring, 0, $length);

                    if (isset($unique[$substring])) {
                        break;
                    }

                    $unique[$substring] = true;
                    yield $substring;
                }
            }
        }
    }

    /**
     * @param iterable<string> $words Words to format.
     * @return Traversable<string> All substrings of the words. May contain duplicates.
     */
    private function formatWords(iterable $words): Traversable
    {
        foreach ($words as $word) {
            for ($start = 0; $start < mb_strlen($word); ++$start) {
                $substring = mb_substr($word, $start, $this->maxLength);

                for ($length = mb_strlen($substring); $this->minLength <= $length; --$length) {
                    yield mb_substr($substring, 0, $length);
                }
            }
        }
    }
}
