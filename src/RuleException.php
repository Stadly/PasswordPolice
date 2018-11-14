<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice;

use RuntimeException;
use Symfony\Component\Translation\Translator;
use Throwable;

/**
 * Exception thrown when a rule could not be enforced.
 */
class RuleException extends RuntimeException
{
    /**
     * @var Rule Rule that could not be enforced.
     */
    private $rule;

    /**
     * Constructor.
     *
     * @param Rule $rule Rule that could not be enforced.
     * @param Throwable $previous Previous exception, used for exception chaining.
     */
    public function __construct(Rule $rule, string $message, ?Throwable $previous = null)
    {
        $this->rule = $rule;

        parent::__construct($message, /*code*/0, $previous);
    }

    public function getRule(): Rule
    {
        return $this->rule;
    }
}
