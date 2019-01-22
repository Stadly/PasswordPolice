<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use RuntimeException;
use Stadly\PasswordPolice\Rule;
use Throwable;

/**
 * Exception thrown if a rule caused an error.
 */
final class RuleException extends RuntimeException
{
    /**
     * @var Rule Rule that caused the error.
     */
    private $rule;

    /**
     * @param Rule $rule Rule that caused the error.
     * @param string $message Exception message.
     * @param Throwable|null $previous Previous exception, used for exception chaining.
     */
    public function __construct(Rule $rule, string $message, ?Throwable $previous = null)
    {
        $this->rule = $rule;

        parent::__construct($message, /*code*/0, $previous);
    }

    /**
     * @return Rule Rule that caused the error.
     */
    public function getRule(): Rule
    {
        return $this->rule;
    }
}
