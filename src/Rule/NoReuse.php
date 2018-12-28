<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use StableSort\StableSort;
use Stadly\PasswordPolice\Constraint\Position;
use Stadly\PasswordPolice\Password;
use Stadly\PasswordPolice\Policy;
use Stadly\PasswordPolice\HashFunction;
use Stadly\PasswordPolice\Rule;
use Stadly\PasswordPolice\ValidationError;

final class NoReuse implements Rule
{
    /**
     * @var HashFunction Hash function.
     */
    private $hashFunction;

    /**
     * @var Position[] Rule constraints.
     */
    private $constraints;

    /**
     * @param HashFunction $hashFunction Hash function to use when comparing passwords.
     * @param int|null $count Number of former passwords to consider.
     * @param int $first First former password to consider.
     * @param int $weight Constraint weight.
     */
    public function __construct(HashFunction $hashFunction, ?int $count = null, int $first = 0, int $weight = 1)
    {
        $this->hashFunction = $hashFunction;
        $this->addConstraint($count, $first, $weight);
    }

    /**
     * @param int|null $count Number of former passwords to consider.
     * @param int $first First former password to consider.
     * @param int $weight Constraint weight.
     * @return $this
     */
    public function addConstraint(?int $count = null, int $first = 0, int $weight = 1): self
    {
        $this->constraints[] = new Position($first, $count, $weight);

        StableSort::usort($this->constraints, static function (Position $a, Position $b): int {
            return $b->getWeight() <=> $a->getWeight();
        });

        return $this;
    }

    /**
     * @return HashFunction Hash function.
     */
    public function getHashFunction(): HashFunction
    {
        return $this->hashFunction;
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
        $positions = $this->getPositions($password);
        $constraint = $this->getViolation($positions, $weight);

        return $constraint === null;
    }

    /**
     * Validate that a password is in compliance with the rule.
     *
     * @param Password|string $password Password to validate.
     * @return ValidationError|null Validation error describing why the password is not in compliance with the rule.
     */
    public function validate($password): ?ValidationError
    {
        $positions = $this->getPositions($password);
        $constraint = $this->getViolation($positions);

        if ($constraint !== null) {
            return new ValidationError(
                $this->getMessage($constraint),
                $password,
                $this,
                $constraint->getWeight()
            );
        }

        return null;
    }

    /**
     * @param int[] $positions Positions of former passwords matching the password.
     * @param int|null $weight Don't consider constraints with lower weights.
     * @return Position|null Constraint violated by the position.
     */
    private function getViolation(array $positions, ?int $weight = null): ?Position
    {
        foreach ($this->constraints as $constraint) {
            if ($weight !== null && $constraint->getWeight() < $weight) {
                continue;
            }
            foreach ($positions as $position) {
                if ($constraint->test($position)) {
                    return $constraint;
                }
            }
        }

        return null;
    }

    /**
     * @param Password|string $password Password to compare with former passwords.
     * @return int[] Positions of former passwords matching the password.
     */
    private function getPositions($password): array
    {
        $positions = [];

        if ($password instanceof Password) {
            $position = 0;
            foreach ($password->getFormerPasswords() as $formerPassword) {
                if ($this->hashFunction->compare((string)$password, (string)$formerPassword)) {
                    $positions[] = $position;
                }
                ++$position;
            }
        }
        return $positions;
    }

    /**
     * @param Position $constraint Constraint that is violated.
     * @return string Message explaining the violation.
     */
    private function getMessage(Position $constraint): string
    {
        $translator = Policy::getTranslator();

        return $translator->trans(
            'Cannot reuse former passwords.'
        );
    }
}
