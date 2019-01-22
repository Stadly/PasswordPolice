<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use DateTimeInterface;
use Stadly\PasswordPolice\Password;
use Stadly\PasswordPolice\Policy;
use Stadly\PasswordPolice\Rule;
use Stadly\PasswordPolice\ValidationError;
use Stadly\PasswordPolice\WordFormatter;
use Traversable;

final class GuessableDataRule implements Rule
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
     * @var (string|DateTimeInterface)[] Guessable data.
     */
    private $guessableData;

    /**
     * @var WordFormatter[] Word formatters.
     */
    private $wordFormatters;

    /**
     * @var int Constraint weight.
     */
    private $weight;

    /**
     * @param (string|DateTimeInterface)[] $guessableData Guessable data.
     * @param WordFormatter[] $wordFormatters Word formatters.
     * @param int $weight Constraint weight.
     */
    public function __construct(array $guessableData = [], array $wordFormatters = [], int $weight = 1)
    {
        $this->guessableData = $guessableData;
        $this->wordFormatters = $wordFormatters;
        $this->weight = $weight;
    }

    /**
     * Check whether a password is in compliance with the rule.
     *
     * @param Password|string $password Password to check.
     * @param int|null $weight Don't consider constraints with lower weights.
     * @return bool Whether the password is in compliance with the rule.
     */
    public function test($password, ?int $weight = 1): bool
    {
        if ($weight !== null && $this->weight < $weight) {
            return true;
        }

        $data = $this->getGuessableData($password);

        return $data === null;
    }

    /**
     * Validate that a password is in compliance with the rule.
     *
     * @param Password|string $password Password to validate.
     * @return ValidationError|null Validation error describing why the password is not in compliance with the rule.
     */
    public function validate($password): ?ValidationError
    {
        $data = $this->getGuessableData($password);

        if ($data !== null) {
            return new ValidationError(
                $this->getMessage($data),
                $password,
                $this,
                $this->weight
            );
        }

        return null;
    }

    /**
     * @param Password|string $password Password to find guessable data in.
     * @return string|DateTimeInterface|null Guessable data in the password.
     */
    private function getGuessableData($password)
    {
        $guessableData = $this->guessableData;
        if ($password instanceof Password) {
            $guessableData = array_merge($guessableData, $password->getGuessableData());
        }

        foreach ($this->getFormattedWords((string)$password) as $word) {
            foreach ($guessableData as $data) {
                if ($this->contains($word, $data)) {
                    return $data;
                }
            }
        }

        return null;
    }

    /**
     * @param string $word Word to format.
     * @return Traversable<string> Formatted words. May contain duplicates.
     */
    private function getFormattedWords(string $word): Traversable
    {
        yield $word;

        foreach ($this->wordFormatters as $wordFormatter) {
            yield from $wordFormatter->apply([$word]);
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
        return mb_stripos($password, $string) !== false;
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
     * @param string|DateTimeInterface $data Data that violates the constraint.
     * @return string Message explaining the violation.
     */
    private function getMessage($data): string
    {
        $translator = Policy::getTranslator();

        return $translator->trans(
            'Must not contain guessable data.'
        );
    }
}
