<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice;

use Symfony\Component\Translation\Translator;
use Stadly\PasswordPolice\Rule\RuleException;
use Stadly\PasswordPolice\Rule\RuleInterface;
use Stadly\PasswordPolice\Rule\TestException;

final class Policy
{
    /**
     * @var RuleInterface[] Policy rules.
     */
    private $rules = [];

    /**
     * @param RuleInterface... $rules Policy rules
     */
    public function __construct(RuleInterface... $rules)
    {
        $this->addRules(...$rules);
    }

    /**
     * @param RuleInterface... $rules Policy rules
     */
    public function addRules(RuleInterface... $rules): void
    {
        foreach ($rules as $rule) {
            $this->rules[] = $rule;
        }
    }

    /**
     * Check whether a password adheres to the policy.
     *
     * @param string $password Password to check.
     * @return bool Whether the password adheres to the policy.
     * @throws TestException If an error occurred while checking the password.
     */
    public function test(string $password): bool
    {
        foreach ($this->rules as $rule) {
            if (!$rule->test($password)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Enforce that a password adheres to the policy.
     *
     * @param string $password Password that must adhere to the policy.
     * @param Translator $translator For translating messages.
     * @throws PolicyException If the password does not adhrere to the policy.
     * @throws TestException If an error occurred while checking the password.
     */
    public function enforce(string $password, Translator $translator): void
    {
        $exceptions = [];

        foreach ($this->rules as $rule) {
            try {
                $rule->enforce($password, $translator);
            } catch (RuleException $exception) {
                $exceptions[] = $exception;
            }
        }

        if ($exceptions !== []) {
            throw new PolicyException($this, $exceptions);
        }
    }
}
