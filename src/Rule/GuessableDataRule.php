<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use DateTimeInterface;
use Stadly\PasswordPolice\DateFormatter;
use Stadly\PasswordPolice\DateFormatter\DefaultFormatter;
use Stadly\PasswordPolice\Password;
use Stadly\PasswordPolice\Policy;
use Stadly\PasswordPolice\Rule;
use Stadly\PasswordPolice\ValidationError;
use Stadly\PasswordPolice\WordFormatter;
use Stadly\PasswordPolice\WordFormatter\FormatterCombiner;
use Traversable;

final class GuessableDataRule implements Rule
{
    /**
     * @var (string|DateTimeInterface)[] Guessable data.
     */
    private $guessableData;

    /**
     * @var WordFormatter Word formatter.
     */
    private $wordFormatter;

    /**
     * @var DateFormatter Date formatter.
     */
    private $dateFormatter;

    /**
     * @var int Constraint weight.
     */
    private $weight;

    /**
     * @param (string|DateTimeInterface)[] $guessableData Guessable data.
     * @param WordFormatter[] $wordFormatters Word formatters.
     * @param DateFormatter|null $dateFormatter Date formatter.
     * @param int $weight Constraint weight.
     */
    public function __construct(
        array $guessableData = [],
        array $wordFormatters = [],
        ?DateFormatter $dateFormatter = null,
        int $weight = 1
    ) {
        $this->guessableData = $guessableData;
        $this->wordFormatter = new FormatterCombiner($wordFormatters);
        $this->dateFormatter = $dateFormatter ?? new DefaultFormatter();
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

        foreach ($this->wordFormatter->apply([(string)$password]) as $word) {
            foreach ($guessableData as $data) {
                if ($this->contains($word, $data)) {
                    return $data;
                }
            }
        }

        return null;
    }

    /**
     * @param string $password Password to check.
     * @param string|DateTimeInterface $data Data to check.
     * @return bool Whether the password contains the data.
     */
    private function contains(string $password, $data): bool
    {
        if ($data instanceof DateTimeInterface) {
            $strings = $this->dateFormatter->apply([$data]);
        } else {
            $strings = [$data];
        }

        foreach ($strings as $string) {
            if (mb_stripos($password, $string) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string|DateTimeInterface $data Data that violates the constraint.
     * @return string Message explaining the violation.
     */
    private function getMessage($data): string
    {
        $translator = Policy::getTranslator();

        return $translator->trans(
            'The password cannot words that are easy to guess.'
        );
    }
}
