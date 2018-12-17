<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use DateTimeInterface;
use InvalidArgumentException;
use RuntimeException;
use Traversable;
use Stadly\PasswordPolice\Password;
use Stadly\PasswordPolice\Policy;
use Stadly\PasswordPolice\WordConverter\WordConverterInterface;
use Stadly\PasswordPolice\WordList\WordListInterface;

final class GuessableData implements RuleInterface
{
    private const DATE_FORMATS = [
        // Year
        ['Y'], // 2018

        // Year month
        ['y', 'n'], // 18 8
        ['y', 'm'], // 18 08
        ['y', 'M'], // 18 Aug
        ['y', 'F'], // 18 August

        // Month year
        ['n', 'y'], // 8 18
        ['M', 'y'], // Aug 18
        ['F', 'y'], // August 18

        // Day month
        ['j', 'n'], // 4 8
        ['j', 'm'], // 4 08
        ['j', 'M'], // 4 Aug
        ['j', 'F'], // 4 August

        // Month day
        ['n', 'j'], // 8 4
        ['n', 'd'], // 8 04
        ['M', 'j'], // Aug 4
        ['M', 'd'], // Aug 04
        ['F', 'j'], // August 4
        ['F', 'd'], // August 04
    ];

    private const DATE_SEPARATORS = [
        '',
        '-',
        ' ',
        '/',
        '.',
        ',',
        '. ',
        ', ',
    ];

    /**
     * @var WordConverterInterface[] Word converters.
     */
    private $wordConverters;

    /**
     * @param WordConverterInterface... $wordConverters Word converters.
     */
    public function __construct(WordConverterInterface... $wordConverters)
    {
        $this->wordConverters = $wordConverters;
    }

    /**
     * Check whether a password is in compliance with the rule.
     *
     * @param Password|string $password Password to check.
     * @return bool Whether the password is in compliance with the rule.
     */
    public function test($password): bool
    {
        $data = $this->getGuessableData($password);

        return $data === null;
    }

    /**
     * Enforce that a password is in compliance with the rule.
     *
     * @param Password|string $password Password that must adhere to the rule.
     * @throws RuleException If the password does not adhrere to the rule.
     */
    public function enforce($password): void
    {
        $data = $this->getGuessableData($password);

        if ($data !== null) {
            throw new RuleException($this, $this->getMessage());
        }
    }

    /**
     * @param Password|string $password Password to find guessable data in.
     * @return string|DateTimeInterface|null Guessable data in the password.
     */
    private function getGuessableData($password)
    {
        if ($password instanceof Password) {
            foreach ($this->getWordsToCheck((string)$password) as $word) {
                foreach ($password->getGuessableData() as $data) {
                    if ($this->contains($word, $data)) {
                        return $data;
                    }
                }
            }
        }
        return null;
    }

    /**
     * @param string $word Word to check.
     * @return Traversable<string> Variants of the word to check.
     */
    private function getWordsToCheck(string $word): Traversable
    {
        $checked = [];
        foreach ($this->getConvertedWords($word) as $wordToCheck) {
            if (isset($checked[$wordToCheck])) {
                continue;
            }

            $checked[$wordToCheck] = true;
            yield $wordToCheck;
        }
    }

    /**
     * @param string $word Word to convert.
     * @return Traversable<string> Converted words. May contain duplicates.
     */
    private function getConvertedWords(string $word): Traversable
    {
        yield $word;

        foreach ($this->wordConverters as $wordConverter) {
            foreach ($wordConverter->convert($word) as $converted) {
                yield $converted;
            }
        }
    }

    /**
     * @param string $password Password to check.
     * @param string|DateTimeInterface $data Data to check.
     * @return bool Whether the password contains the data.
     */
    private function contains(string $password, $data): bool
    {
        if ($data instanceof DateTimeInterface) {
            return $this->containsDate($password, $data);
        }

        return $this->containsString($password, $data);
    }

    /**
     * @param string $password Password to check.
     * @param string $string String to check.
     * @return bool Whether the password contains the string.
     */
    private function containsString(string $password, string $string): bool
    {
        return false !== mb_stripos($password, $string);
    }

    /**
     * @param string $password Password to check.
     * @param DateTimeInterface $date Date to check.
     * @return bool Whether the password contains the date.
     */
    private function containsDate(string $password, DateTimeInterface $date): bool
    {
        foreach ($this->getDateFormats() as $format) {
            if ($this->containsString($password, $date->format($format))) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return iterable<string> Date formats.
     */
    private function getDateFormats(): iterable
    {
        foreach (self::DATE_FORMATS as $format) {
            foreach (self::DATE_SEPARATORS as $separator) {
                yield implode($separator, $format);
            }
        }
    }

    /**
     * @return string Message explaining the violation.
     */
    private function getMessage(): string
    {
        $translator = Policy::getTranslator();

        return $translator->trans(
            'Must not contain guessable data.'
        );
    }
}
