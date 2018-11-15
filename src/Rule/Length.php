<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use InvalidArgumentException;
use Stadly\PasswordPolice\Rule;
use Symfony\Component\Translation\Translator;

final class Length implements Rule
{
    /**
     * @var int Minimum password length.
     */
    private $min;

    /**
     * @var int|null Maximum password length.
     */
    private $max;

    public function __construct(int $min, ?int $max = null)
    {
        if ($min < 0) {
            throw new InvalidArgumentException('Min cannot be negative.');
        }
        if ($max !== null && $max < $min) {
            throw new InvalidArgumentException('Max cannot be smaller than min.');
        }
        if ($min === 0 && $max === null) {
            throw new InvalidArgumentException('Min cannot be zero when max is unconstrained.');
        }

        $this->min = $min;
        $this->max = $max;
    }

    public function getMin(): int
    {
        return $this->min;
    }

    public function getMax(): ?int
    {
        return $this->max;
    }

    public function test(string $password): bool
    {
        if (mb_strlen($password) < $this->min) {
            return false;
        }

        if (null !== $this->max && $this->max < mb_strlen($password)) {
            return false;
        }

        return true;
    }

    /**
     * @throws LengthException If the rule cannot be enforced.
     */
    public function enforce(string $password, Translator $translator): void
    {
        if (!$this->test($password)) {
            throw new LengthException($this, mb_strlen($password), $translator);
        }
    }
}
