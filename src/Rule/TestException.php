<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use RuntimeException;
use Symfony\Component\Translation\Translator;
use Throwable;

/**
 * Exception thrown if an error occurred while testing a rule.
 */
class TestException extends RuntimeException
{
    /**
     * @var RuleInterface Rule that was tested when the error occurred.
     */
    private $rule;

    /**
     * @param RuleInterface $rule Rule that was tested when the error occurred.
     * @param string $message Exception message.
     * @param Throwable|null $previous Previous exception, used for exception chaining.
     */
    public function __construct(RuleInterface $rule, string $message, ?Throwable $previous = null)
    {
        $this->rule = $rule;

        parent::__construct($message, /*code*/0, $previous);
    }

    /**
     * @return RuleInterface Rule that was tested when the error occurred.
     */
    public function getRule(): RuleInterface
    {
        return $this->rule;
    }
}
