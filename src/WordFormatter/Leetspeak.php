<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordFormatter;

use Stadly\PasswordPolice\WordFormatter;
use Traversable;

final class Leetspeak implements WordFormatter
{
    /**
     * @var array<string, string[]>
     */
    private $encodeMap = [
        'A' => ['4', '@', '∂'],
        'B' => ['8', 'ß'],
        'C' => ['(', '¢', '<', '[', '©'],
        'D' => ['∂'],
        'E' => ['3', '€', 'є'],
        'F' => ['ƒ'],
        'G' => ['6', '9'],
        'H' => ['#'],
        'I' => ['1', '!', '|', ':'],
        'J' => ['¿'],
        'K' => ['X'],
        'L' => ['1', '£', 'ℓ'],
        'O' => ['0', '°'],
        'R' => ['2', '®', 'Я'],
        'S' => ['5', '$', '§'],
        'T' => ['7', '†'],
        'U' => ['µ'],
        'W' => ['vv'],
        'X' => ['×'],
        'Y' => ['φ', '¥'],
        'Z' => ['2', '≥'],
    ];

    /**
     * @var array<string|int, string[]>
     */
    private $decodeMap = [];

    public function __construct()
    {
        foreach ($this->encodeMap as $char => $encodedChars) {
            foreach ($encodedChars as $encodedChar) {
                $this->decodeMap[$encodedChar][] = $char;
            }
        }
    }

    /**
     * @param string $word Word to get decode map for.
     * @return array<string|int, string[]> Map for decoding the word prefix.
     */
    private function getDecodeMap(string $word): array
    {
        $decodeMap = [];
        foreach ($this->decodeMap as $encodedChar => $chars) {
            if ((string)$encodedChar === mb_substr($word, 0, mb_strlen((string)$encodedChar))) {
                $decodeMap[$encodedChar] = $chars;
            }
        }
        $decodeMap[mb_substr($word, 0, 1)][] = mb_substr($word, 0, 1);

        return $decodeMap;
    }

    /**
     * {@inheritDoc}
     */
    public function apply(string $word): Traversable
    {
        if ($word === '') {
            yield '';
            return;
        }

        foreach ($this->getDecodeMap($word) as $encodedChar => $chars) {
            foreach ($this->apply(mb_substr($word, mb_strlen((string)$encodedChar))) as $suffix) {
                foreach ($chars as $char) {
                    yield $char.$suffix;
                }
            }
        }
    }
}
