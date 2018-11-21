<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use Symfony\Component\Translation\Translator;

/**
 * Interface that must be implemented by all rules.
 */
interface RuleInterface
{
    /**
     * Check whether a password adheres to the rule.
     *
     * @param string $password Password to check.
     * @return bool Whether the password adheres to the rule.
     * @throws TestException If an error occurred while testing the rule.
     */
    public function test(string $password): bool;

    /**
     * Enforce that a password adheres to the rule.
     *
     * @param string $password Password that must adhere to the rule.
     * @param Translator $translator For translating messages.
     * @throws RuleException If the password does not adhrere to the rule.
     * @throws TestException If an error occurred while testing the rule.
     */
    public function enforce(string $password, Translator $translator): void;

    /**
     * @return string Message explaining the requirements of the rule.
     */
    public function getMessage(Translator $translator): string;
}
