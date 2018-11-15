<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice;

use Stadly\PasswordPolice\Rule\RuleException;
use Symfony\Component\Translation\Translator;

interface Rule
{
    public function test(string $password): bool;

    /**
     * @param string $password Password that must adhere to the rule.
     * @param Translator $translator For translating messages.
     * @throws RuleException If the rule cannot be enforced.
     */
    public function enforce(string $password, Translator $translator): void;

    public function getMessage(Translator $translator): string;
}
