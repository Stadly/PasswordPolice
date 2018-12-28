<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use RuntimeException;
use Stadly\PasswordPolice\Rule;
use Throwable;

/**
 * Exception thrown if an error occurred while testing a rule.
 */
final class TestException extends RuntimeException
{
    /**
     * @var Rule Rule that was tested when the error occurred.
     */
    private $rule;

    /**
     * @param Rule $rule Rule that was tested when the error occurred.
     * @param string $message Exception message.
     * @param Throwable|null $previous Previous exception, used for exception chaining.
     */
    public function __construct(Rule $rule, string $message, ?Throwable $previous = null)
    {
        $this->rule = $rule;

        parent::__construct($message, /*code*/0, $previous);
    }

    /**
     * @return Rule Rule that was tested when the error occurred.
     */
    public function getRule(): Rule
    {
        return $this->rule;
    }
}
