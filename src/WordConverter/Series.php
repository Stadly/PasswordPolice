<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordConverter;

use InvalidArgumentException;
use Stadly\PasswordPolice\WordConverter;
use Traversable;

final class Series implements WordConverter
{
    /**
     * @var WordConverter[] Word converters.
     */
    private $wordConverters;

    /**
     * @param WordConverter ...$wordConverters Word converters.
     */
    public function __construct(WordConverter ...$wordConverters)
    {
        if ($wordConverters === []) {
            throw new InvalidArgumentException('At least one word converter must be specified.');
        }

        $this->wordConverters = $wordConverters;
    }

    /**
     * {@inheritDoc}
     */
    public function convert(string $word): Traversable
    {
        yield from $this->convertWord($word, $this->wordConverters);
    }

    private function convertWord(string $word, array $wordConverters): Traversable
    {
        $wordConverter = array_shift($wordConverters);

        if ($wordConverters === []) {
            yield from $wordConverter->convert($word);
            return;
        }

        foreach ($wordConverter->convert($word) as $converted) {
            yield from $this->convertWord($converted, $wordConverters);
        }
    }
}
