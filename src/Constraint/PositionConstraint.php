<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Constraint;

use InvalidArgumentException;

final class PositionConstraint
{
    /**
     * @var int First position.
     */
    private $first;

    /**
     * @var int|null Number of positions.
     */
    private $count;

    /**
     * @var int Constraint weight.
     */
    private $weight;

    /**
     * @param int $first First position.
     * @param int|null $count Number of positions.
     * @param int $weight Constraint weight.
     */
    public function __construct(int $first = 0, ?int $count = null, int $weight = 1)
    {
        if ($first < 0) {
            throw new InvalidArgumentException('First cannot be negative.');
        }
        if ($count !== null && $count < 1) {
            throw new InvalidArgumentException('Count must be positive.');
        }

        $this->first = $first;
        $this->count = $count;
        $this->weight = $weight;
    }

    /**
     * @return int First position.
     */
    public function getFirst(): int
    {
        return $this->first;
    }

    /**
     * @return int|null Number of positions.
     */
    public function getCount(): ?int
    {
        return $this->count;
    }

    /**
     * @return int Constraint weight.
     */
    public function getWeight(): int
    {
        return $this->weight;
    }

    /**
     * Check whether the position is in compliance with the constraint.
     *
     * @param int $pos Position to check.
     * @return bool Whether the position is in compliance with the constraint.
     */
    public function test(int $pos): bool
    {
        if ($pos < $this->first) {
            return false;
        }

        if ($this->count !== null && $this->first+$this->count <= $pos) {
            return false;
        }

        return true;
    }
}
