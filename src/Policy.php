<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice;

use Stadly\PasswordPolice\Rule\RuleException;
use Stadly\PasswordPolice\Rule\RuleInterface;
use Stadly\PasswordPolice\Rule\TestException;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\TranslatorInterface;

final class Policy
{
    /**
     * @var RuleInterface[] Policy rules.
     */
    private $rules = [];

    /**
     * @var TranslatorInterface|null Translator for translating messages.
     */
    private static $translator;

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
     * @throws PolicyException If the password does not adhrere to the policy.
     * @throws TestException If an error occurred while checking the password.
     */
    public function enforce(string $password): void
    {
        $exceptions = [];

        foreach ($this->rules as $rule) {
            try {
                $rule->enforce($password);
            } catch (RuleException $exception) {
                $exceptions[] = $exception;
            }
        }

        if ($exceptions !== []) {
            throw new PolicyException($this, $exceptions);
        }
    }

    /**
     * @param TranslatorInterface|null $translator Translator for translating messages.
     */
    public static function setTranslator(?TranslatorInterface $translator): void
    {
        self::$translator = $translator;
    }

    /**
     * @return TranslatorInterface Translator for translating messages.
     */
    public static function getTranslator(): TranslatorInterface
    {
        if (null === self::$translator) {
            self::$translator = new Translator('en_US');
        }
        return self::$translator;
    }
}
