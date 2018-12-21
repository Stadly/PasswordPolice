<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use StableSort\StableSort;
use Stadly\PasswordPolice\Constraint\Count;
use Stadly\PasswordPolice\Password;
use Stadly\PasswordPolice\Policy;
use Stadly\PasswordPolice\ValidationError;

final class LowerCase implements RuleInterface
{
    /**
     * @var Count[] Rule constraints.
     */
    private $constraints;

    /**
     * @param int $min Minimum number of lower case letters.
     * @param int|null $max Maximum number of lower case letters.
     * @param int $weight Constraint weight.
     */
    public function __construct(int $min = 1, ?int $max = null, int $weight = 1)
    {
        $this->addConstraint($min, $max, $weight);
    }

    /**
     * @param int $min Minimum number of lower case letters.
     * @param int|null $max Maximum number of lower case letters.
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
     * Check whether a password is in compliance with the rule.
     *
     * @param Password|string $password Password to check.
     * @param int|null $weight Don't consider constraints with lower weights.
     * @return bool Whether the password is in compliance with the rule.
     */
    public function test($password, ?int $weight = 1): bool
    {
        $count = $this->getCount((string)$password);
        $constraint = $this->getViolation($count, $weight);

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
        $count = $this->getCount((string)$password);
        $constraint = $this->getViolation($count);

        if ($constraint !== null) {
            return new ValidationError($this, $constraint->getWeight(), $this->getMessage($constraint, $count));
        }

        return null;
    }

    /**
     * @param int $count Number of lower case characters.
     * @param int|null $weight Don't consider constraints with lower weights.
     * @return Count|null Constraint violated by the count.
     */
    private function getViolation(int $count, ?int $weight = null): ?Count
    {
        foreach ($this->constraints as $constraint) {
            if ($weight !== null && $constraint->getWeight() < $weight) {
                continue;
            }
            if (!$constraint->test($count)) {
                return $constraint;
            }
        }

        return null;
    }

    /**
     * @param string $password Password to count characters in.
     * @return int Number of lower case characters.
     */
    private function getCount(string $password): int
    {
        $upperCase = mb_strtoupper($password);

        $passwordCharacters = $this->splitString($password);
        $upperCaseCharacters = $this->splitString($upperCase);
        assert(count($passwordCharacters) === count($upperCaseCharacters));

        $count = 0;
        for ($i = count($passwordCharacters)-1; $i >= 0; --$i) {
            if ($passwordCharacters[$i] !== $upperCaseCharacters[$i]) {
                ++$count;
            }
        }

        return $count;
    }

    /**
     * @param string $string String to split into individual characters.
     * @return string[] Array of characters.
     */
    private function splitString(string $string): array
    {
        $characters = preg_split('{}u', $string, -1, PREG_SPLIT_NO_EMPTY);
        assert($characters !== false);

        return $characters;
    }

    /**
     * @param Count $constraint Constraint that is violated.
     * @param int $count Count that violates the constraint.
     * @return string Message explaining the violation.
     */
    private function getMessage(Count $constraint, int $count): string
    {
        $translator = Policy::getTranslator();

        if ($constraint->getMax() === null) {
            return $translator->trans(
                'There must be at least one lower case character.|'.
                'There must be at least %count% lower case characters.',
                ['%count%' => $constraint->getMin()]
            );
        }

        if ($constraint->getMax() === 0) {
            return $translator->trans(
                'There must be no lower case characters.'
            );
        }

        if ($constraint->getMin() === 0) {
            return $translator->trans(
                'There must be at most one lower case character.|'.
                'There must be at most %count% lower case characters.',
                ['%count%' => $constraint->getMax()]
            );
        }

        if ($constraint->getMin() === $constraint->getMax()) {
            return $translator->trans(
                'There must be exactly one lower case character.|'.
                'There must be exactly %count% lower case characters.',
                ['%count%' => $constraint->getMin()]
            );
        }

        return $translator->trans(
            'There must be between %min% and %max% lower case characters.',
            ['%min%' => $constraint->getMin(), '%max%' => $constraint->getMax()]
        );
    }
}
