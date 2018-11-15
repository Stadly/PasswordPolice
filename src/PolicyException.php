<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice;

use RuntimeException;
use Stadly\PasswordPolice\Rule\RuleException;
use Symfony\Component\Translation\Translator;
use Throwable;

/**
 * Exception thrown when a policy could not be enforced.
 */
final class PolicyException extends RuntimeException
{
    /**
     * @var Policy Policy that could not be enforced.
     */
    private $policy;

    /**
     * @var RuleException[] Exceptions thrown by rules that could not be enforced.
     */
    private $ruleExceptions = [];

    /**
     * @param Policy $policy Policy that could not be enforced.
     * @param RuleException[] $ruleExceptions Exceptions thrown by rules that could not be enforced.
     * @param Throwable $previous Previous exception, used for exception chaining.
     */
    public function __construct(Policy $policy, array $ruleExceptions, ?Throwable $previous = null)
    {
        $this->policy = $policy;
        $this->ruleExceptions = $ruleExceptions;

        $messages = [];
        foreach ($ruleExceptions as $ruleException) {
            $messages[] = $ruleException->getMessage();
        }

        parent::__construct(implode(' ', $messages), /*code*/0, $previous);
    }

    /**
     * @return Policy Policy that could not be enforced.
     */
    public function getPolicy(): Policy
    {
        return $this->policy;
    }

    /**
     * @return RuleException[] Exceptions thrown by rules in the policy that could not be enforced.
     */
    public function getRuleExceptions(): array
    {
        return $this->ruleExceptions;
    }
}
