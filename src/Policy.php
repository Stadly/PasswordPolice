<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice;

use Stadly\PasswordPolice\Rule\CouldNotUseRuleException;
use Symfony\Component\Translation\Loader\MoFileLoader;
use Symfony\Component\Translation\Translator;
use Symfony\Contracts\Translation\LocaleAwareInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class Policy
{
    /**
     * @var array<Rule> Policy rules.
     */
    private $rules = [];

    /**
     * @var (TranslatorInterface&LocaleAwareInterface)|null Translator for translating messages.
     */
    private $translator = null;

    /**
     * @param Rule ...$rules Policy rules.
     */
    public function __construct(Rule ...$rules)
    {
        $this->addRules(...$rules);
    }

    /**
     * @param Rule ...$rules Policy rules.
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
     * @param int|null $weight Don't consider rule constraints with lower weights.
     * @return bool Whether the password is in compliance with the policy.
     * @throws CouldNotUseRuleException If an error occurred.
     */
    public function test($password, ?int $weight = null): bool
    {
        foreach ($this->rules as $rule) {
            if (!$rule->test($password, $weight)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validate that a password is in compliance with the policy.
     *
     * @param Password|string $password Password to validate.
     * @return array<ValidationError> Validation errors describing why the password isn't in compliance with the policy.
     * @throws CouldNotUseRuleException If an error occurred.
     */
    public function validate($password): array
    {
        $validationErrors = [];

        foreach ($this->rules as $rule) {
            $validationError = $rule->validate($password, $this->getTranslator());

            if ($validationError !== null) {
                $validationErrors[] = $validationError;
            }
        }

        return $validationErrors;
    }

    /**
     * @param (TranslatorInterface&LocaleAwareInterface)|null $translator Translator for translating messages.
     */
    public function setTranslator(?TranslatorInterface $translator): void
    {
        $this->translator = $translator;
    }

    /**
     * @return TranslatorInterface&LocaleAwareInterface Translator to use for the current instance.
     */
    public function getTranslator(): TranslatorInterface
    {
        if ($this->translator === null) {
            $this->translator = new Translator('en_US');
            $this->translator->addLoader('mo', new MoFileLoader());
            $this->translator->addResource('mo', __DIR__ . '/../translations/messages.nn_NO.mo', 'nn_NO');
            $this->translator->addResource('mo', __DIR__ . '/../translations/messages.nb_NO.mo', 'nb_NO');
        }

        return $this->translator;
    }
}
