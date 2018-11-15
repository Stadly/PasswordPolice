<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use RuntimeException;
use Symfony\Component\Translation\Translator;
use Throwable;

/**
 * Exception thrown when a rule could not be enforced.
 */
class RuleException extends RuntimeException
{
    /**
     * @var RuleInterface Rule that could not be enforced.
     */
    private $rule;

    /**
     * @param RuleInterface $rule Rule that could not be enforced.
     * @param string $message Exception message.
     * @param Throwable $previous Previous exception, used for exception chaining.
     */
    public function __construct(RuleInterface $rule, string $message, ?Throwable $previous = null)
    {
        $this->rule = $rule;

        parent::__construct($message, /*code*/0, $previous);
    }

    /**
     * @return RuleInterface Rule that could not be enforced.
     */
    public function getRule(): RuleInterface
    {
        return $this->rule;
    }
}
