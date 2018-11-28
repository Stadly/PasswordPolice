<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use Stadly\PasswordPolice\Password;

/**
 * Interface that must be implemented by all rules.
 */
interface RuleInterface
{
    /**
     * Check whether a password adheres to the rule.
     *
     * @param Password|string $password Password to check.
     * @return bool Whether the password adheres to the rule.
     * @throws TestException If an error occurred while testing the rule.
     */
    public function test($password): bool;

    /**
     * Enforce that a password adheres to the rule.
     *
     * @param Password|string $password Password that must adhere to the rule.
     * @throws RuleException If the password does not adhrere to the rule.
     * @throws TestException If an error occurred while testing the rule.
     */
    public function enforce($password): void;

    /**
     * @return string Message explaining the requirements of the rule.
     */
    public function getMessage(): string;
}
