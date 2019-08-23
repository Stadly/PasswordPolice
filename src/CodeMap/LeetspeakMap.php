<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\CodeMap;

use Stadly\PasswordPolice\CodeMap;

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
     * @var array<int, array<string|int, array<string>>> Leetspeak code map.
     */
    private $codeMap = [];

    /**
     * @param bool $encode Whether the map should encode or decode Leetspeak.
     */
    public function __construct(bool $encode = false)
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
    }

    /**
     * @inheritDoc
     */
    public function getLengths(): array
    {
        return array_keys($this->codeMap);
    }

    /**
     * @inheritDoc
     */
    public function code(string $string): array
    {
        // Add coded characters.
        $codeMap = $this->codeMap[mb_strlen($string)][mb_strtoupper($string)] ?? [];

        // Add uncoded character.
        $codeMap[] = $string;

        return $codeMap;
    }
}
