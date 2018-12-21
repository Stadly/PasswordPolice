<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice;

use Stadly\PasswordPolice\Rule\RuleInterface;

final class ValidationError
{
    /**
     * @var RuleInterface Rule that could not be validated.
     */
    private $rule;

    /**
     * @var int Weight of violated constraint.
     */
    private $weight;

    /**
     * @var string Message describing why the rule could not be validated.
     */
    private $message;

    /**
     * @param RuleInterface $rule Rule that could not be validated.
     * @param int $weight Weight of violated constraint.
     * @param string $message Message describing why the rule could not be validated.
     */
    public function __construct(RuleInterface $rule, int $weight, string $message)
    {
        $this->rule = $rule;
        $this->weight = $weight;
        $this->message = $message;
    }

    /**
     * @return RuleInterface Rule that could not be validated.
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

    /**
     * @return string Message describing why the rule could not be validated.
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}
