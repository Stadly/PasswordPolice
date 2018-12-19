<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use RuntimeException;
use Throwable;

/**
 * Exception thrown when a rule could not be enforced.
 */
final class RuleException extends RuntimeException
{
    /**
     * @var RuleInterface Rule that could not be enforced.
     */
    private $rule;

    /**
     * @var int Weight of violated constraint.
     */
    private $weight;

    /**
     * @param RuleInterface $rule Rule that could not be enforced.
     * @param int $weight Weight of violated constraint.
     * @param string $message Exception message.
     * @param Throwable|null $previous Previous exception, used for exception chaining.
     */
    public function __construct(RuleInterface $rule, int $weight, string $message, ?Throwable $previous = null)
    {
        $this->rule = $rule;
        $this->weight = $weight;

        parent::__construct($message, /*code*/0, $previous);
    }

    /**
     * @return RuleInterface Rule that could not be enforced.
     */
    public function getRule(): RuleInterface
    {
        return $this->rule;
    }

    /**
     * @return int Weight of violated constraint.
     */
    public function getWeight(): int
    {
        return $this->weight;
    }
}
