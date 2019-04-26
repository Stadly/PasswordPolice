<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice;

use Stadly\PasswordPolice\Rule\RuleException;
use Symfony\Component\Translation\Translator;
use Symfony\Contracts\Translation\LocaleAwareInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class Policy
{
    /**
     * @var Rule[] Policy rules.
     */
    private $rules = [];

    /**
     * @var (TranslatorInterface&LocaleAwareInterface)|null Translator for translating messages.
     */
    private static $translator = null;

    /**
     * @param Rule ...$rules Policy rules.
     */
    public function __construct(Rule ...$rules)
    {
        $this->addRules(...$rules);
    }

    /**
     * @param Rule ...$rules Policy rules
     */
    public function addRules(Rule ...$rules): void
    {
        foreach ($rules as $rule) {
            $this->rules[] = $rule;
        }
    }

    /**
     * Check whether a password is in compliance with the policy.
     *
     * @param Password|string $password Password to check.
     * @return bool Whether the password is in compliance with the policy.
     * @throws RuleException If an error occurred.
     */
    public function test($password): bool
    {
        foreach ($this->rules as $rule) {
            if (!$rule->test($password)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validate that a password is in compliance with the policy.
     *
     * @param Password|string $password Password to validate.
     * @return ValidationError[] Validation errors describing why the password is not in compliance with the policy.
     * @throws RuleException If an error occurred.
     */
    public function validate($password): array
    {
        $validationErrors = [];

        foreach ($this->rules as $rule) {
            $validationError = $rule->validate($password);

            if ($validationError !== null) {
                $validationErrors[] = $validationError;
            }
        }

        return $validationErrors;
    }

    /**
     * @param (TranslatorInterface&LocaleAwareInterface)|null $translator Translator for translating messages.
     */
    public static function setTranslator($translator): void
    {
        self::$translator = $translator;
    }

    /**
     * @return TranslatorInterface&LocaleAwareInterface Translator for translating messages.
     */
    public static function getTranslator()
    {
        if (self::$translator === null) {
            self::$translator = new Translator('en_US');
        }
        return self::$translator;
    }
}
