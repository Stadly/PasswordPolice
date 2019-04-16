<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\CodeMap;

use Stadly\PasswordPolice\CharTree;
use Stadly\PasswordPolice\CodeMap;
use Stadly\PasswordPolice\Formatter\LengthFilter;
use Stadly\PasswordPolice\Formatter\Truncator;

final class LeetspeakMap implements CodeMap
{
    private const ENCODE_MAP = [
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
     * @var array<int, array<string|int, string[]>> Leetspeak code map.
     */
    private $codeMap = [];

    /**
     * @var Truncator[] Formatter for extracting the first characters.
     */
    private $charExtractors;

    /**
     * @param bool $encode Whether the map should encode or decode Leetspeak.
     */
    public function __construct($encode = false)
    {
        if ($encode) {
            foreach (self::ENCODE_MAP as $char => $codedChars) {
                $this->codeMap[mb_strlen($char)][mb_strtoupper($char)] = $codedChars;
            }
        } else {
            foreach (self::ENCODE_MAP as $char => $codedChars) {
                foreach ($codedChars as $codedChar) {
                    $this->codeMap[mb_strlen($codedChar)][mb_strtoupper($codedChar)][] = $char;
                }
            }
        }
        foreach (array_keys($this->codeMap + [1 => true]) as $length) {
            $truncator = new Truncator($length);
            $truncator->setNext(new LengthFilter($length, $length));
            $this->charExtractors[$length] = $truncator;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getMap(CharTree $charTree): array
    {
        $codeMap = [];

        // Add coded characters.
        foreach ($this->codeMap as $length => $chars) {
            foreach ($this->charExtractors[$length]->apply($charTree) as $char) {
                if (isset($chars[mb_strtoupper($char)])) {
                    $codeMap[$char] = $chars[mb_strtoupper($char)];
                }
            }
        }

        // Add uncoded characters.
        foreach ($this->charExtractors[1]->apply($charTree) as $char) {
            $codeMap[$char][] = $char;
        }

        return $codeMap;
    }
}
