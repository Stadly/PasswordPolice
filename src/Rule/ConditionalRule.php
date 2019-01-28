<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use DateTime;
use DateTimeInterface;
use StableSort\StableSort;
use Stadly\PasswordPolice\Constraint\DateConstraint;
use Stadly\PasswordPolice\Password;
use Stadly\PasswordPolice\Policy;
use Stadly\PasswordPolice\Rule;
use Stadly\PasswordPolice\ValidationError;

final class ConditionalRule implements Rule
{
    /**
     * @var Rule Rule to test conditionally.
     */
    private $rule;

    /**
     * @var callable(Password|string): bool Condition function.
     */
    private $condition;

    /**
     * @param Rule $rule Rule to test if the condition is true.
     * @param callable(Password|string): bool $condition Condition function.
     */
    public function __construct(Rule $rule, callable $condition)
    {
        $this->rule = $rule;
        $this->condition = $condition;
    }

    /**
     * {@inheritDoc}
     */
    public function test($password, ?int $weight = 1): bool
    {
        if (($this->condition)($password)) {
            return $this->rule->test($password, $weight);
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function validate($password): ?ValidationError
    {
        if (($this->condition)($password)) {
            return $this->rule->validate($password);
        }

        return null;
    }
}
