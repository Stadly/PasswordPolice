<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use Stadly\PasswordPolice\RuleException;
use Symfony\Component\Translation\Translator;
use Throwable;

/**
 * Exception thrown when a length rule could not be enforced.
 */
final class LengthException extends RuleException
{
    /**
     * @var int Number of characters.
     */
    private $count;

    /**
     * Constructor.
     *
     * @param Length $rule Rule that could not be enforced.
     * @param int $count Number of characters.
     * @param Translator $translator For translating messages.
     * @param Throwable $previous Previous exception, used for exception chaining.
     */
    public function __construct(Length $rule, int $count, Translator $translator, ?Throwable $previous = null)
    {
        $this->count = $count;

        if ($rule->getMax() === null) {
            $message = $translator->transChoice(
                'There must be at least one character.|'.
                'There must be at least %count% characters.',
                $rule->getMin()
            );
        } elseif ($rule->getMax() === 0) {
            $message = $translator->trans(
                'There must be no characters.'
            );
        } elseif ($rule->getMin() === 0) {
            $message = $translator->transChoice(
                'There must be at most one character.|'.
                'There must be at most %count% characters.',
                $rule->getMax()
            );
        } elseif ($rule->getMin() === $rule->getMax()) {
            $message = $translator->transChoice(
                'There must be exactly one character.|'.
                'There must be exactly %count% characters.',
                $rule->getMin()
            );
        } else {
            $message = $translator->trans(
                'There must be between %min% and %max% characters.',
                ['%min%' => $rule->getMin(), '%max%' => $rule->getMax()]
            );
        }

        parent::__construct($rule, $message, $previous);
    }

    public function getCount(): int
    {
        return $this->count;
    }
}
