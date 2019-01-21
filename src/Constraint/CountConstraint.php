<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Constraint;

use InvalidArgumentException;

final class CountConstraint
{
    /**
     * @var int Minimum count.
     */
    private $min;

    /**
     * @var int|null Maximum count.
     */
    private $max;

    /**
     * @var int Constraint weight.
     */
    private $weight;

    /**
     * @param int $min Minimum count.
     * @param int|null $max Maximum count.
     * @param int $weight Constraint weight.
     */
    public function __construct(int $min = 1, ?int $max = null, int $weight = 1)
    {
        if ($min < 0) {
            throw new InvalidArgumentException('Min cannot be negative.');
        }
        if ($max !== null && $max < $min) {
            throw new InvalidArgumentException('Max cannot be smaller than min.');
        }

        $this->min = $min;
        $this->max = $max;
        $this->weight = $weight;
    }

    /**
     * @return int Minimum count.
     */
    public function getMin(): int
    {
        return $this->min;
    }

    /**
     * @return int|null Maximum count.
     */
    public function getMax(): ?int
    {
        return $this->max;
    }

    /**
     * @return int Constraint weight.
     */
    public function getWeight(): int
    {
        return $this->weight;
    }

    /**
     * Check whether the count is in compliance with the constraint.
     *
     * @param int $count Count to check.
     * @return bool Whether the count is in compliance with the constraint.
     */
    public function test(int $count): bool
    {
        if ($count < $this->min) {
            return false;
        }

        if ($this->max !== null && $this->max < $count) {
            return false;
        }

        return true;
    }
}
