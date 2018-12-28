<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Constraint;

use DateTimeInterface;
use InvalidArgumentException;

final class Date
{
    /**
     * @var DateTimeInterface|null Minimum date.
     */
    private $min;

    /**
     * @var DateTimeInterface|null Maximum date.
     */
    private $max;

    /**
     * @var int Constraint weight.
     */
    private $weight;

    /**
     * @param DateTimeInterface|null $min Minimum date.
     * @param DateTimeInterface|null $max Maximum date.
     * @param int $weight Constraint weight.
     */
    public function __construct(?DateTimeInterface $min, ?DateTimeInterface $max = null, int $weight = 1)
    {
        if ($min !== null && $max !== null && $max < $min) {
            throw new InvalidArgumentException('Max cannot be smaller than min.');
        }

        $this->min = $min;
        $this->max = $max;
        $this->weight = $weight;
    }

    /**
     * @return DateTimeInterface|null Minimum date.
     */
    public function getMin(): ?DateTimeInterface
    {
        return $this->min;
    }

    /**
     * @return DateTimeInterface|null Maximum date.
     */
    public function getMax(): ?DateTimeInterface
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
     * Check whether the date is in compliance with the constraint.
     *
     * @param DateTimeInterface $date Date to check.
     * @return bool Whether the date is in compliance with the constraint.
     */
    public function test(DateTimeInterface $date): bool
    {
        if ($this->min !== null && $date < $this->min) {
            return false;
        }

        if ($this->max !== null && $this->max < $date) {
            return false;
        }

        return true;
    }
}
