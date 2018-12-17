<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use InvalidArgumentException;
use StableSort\StableSort;
use Stadly\PasswordPolice\Constraint\Count;
use Stadly\PasswordPolice\Password;
use Stadly\PasswordPolice\Policy;

abstract class CharacterClass implements RuleInterface
{
    /**
     * @var string Characters matched by the rule.
     */
    protected $characters;

    /**
     * @var Count[] Rule constraints.
     */
    private $constraints;

    /**
     * @param string $characters Characters matched by the rule.
     * @param int $min Minimum number of characters matching the rule.
     * @param int|null $max Maximum number of characters matching the rule.
     * @param int $weight Constraint weight.
     */
    public function __construct(string $characters, int $min = 1, ?int $max = null, int $weight = 1)
    {
        if ($characters === '') {
            throw new InvalidArgumentException('At least one character must be specified.');
        }

        $this->characters = $characters;
        $this->addConstraint($min, $max, $weight);
    }

    /**
     * @param int $min Minimum number of characters matching the rule.
     * @param int|null $max Maximum number of characters matching the rule.
     * @param int $weight Constraint weight.
     * @return $this
     */
    public function addConstraint(int $min = 1, ?int $max = null, int $weight = 1): self
    {
        $this->constraints[] = new Count($min, $max, $weight);

        StableSort::usort($this->constraints, function (Count $a, Count $b): int {
            return $b->getWeight() <=> $a->getWeight();
        });

        return $this;
    }

    /**
     * @return string Characters matched by the rule.
     */
    public function getCharacters(): string
    {
        return $this->characters;
    }

    /**
     * Check whether a password is in compliance with the rule.
     *
     * @param Password|string $password Password to check.
     * @return bool Whether the password is in compliance with the rule.
     */
    public function test($password): bool
    {
        $count = $this->getCount((string)$password);
        $constraint = $this->getViolation($count);

        return $constraint === null;
    }

    /**
     * Enforce that a password is in compliance with the rule.
     *
     * @param Password|string $password Password that must adhere to the rule.
     * @throws RuleException If the password does not adhrere to the rule.
     */
    public function enforce($password): void
    {
        $count = $this->getCount((string)$password);
        $constraint = $this->getViolation($count);

        if ($constraint !== null) {
            throw new RuleException($this, $this->getMessage($constraint, $count));
        }
    }

    /**
     * @param int $count Number of characters matching the rule.
     * @return Count|null Constraint violated by the count.
     */
    private function getViolation(int $count): ?Count
    {
        foreach ($this->constraints as $constraint) {
            if (!$constraint->test($count)) {
                return $constraint;
            }
        }

        return null;
    }

    /**
     * @param string $password Password to count characters in.
     * @return int Number of characters matching the rule.
     */
    private function getCount(string $password): int
    {
        $escapedCharacters = preg_quote($this->characters);
        $count = preg_match_all('{['.$escapedCharacters.']}u', $password);
        assert(false !== $count);

        return $count;
    }

    /**
     * @param Count $constraint Constraint that is violated.
     * @param int $count Count that violates the constraint.
     * @return string Message explaining the violation.
     */
    abstract protected function getMessage(Count $constraint, int $count): string;
}
