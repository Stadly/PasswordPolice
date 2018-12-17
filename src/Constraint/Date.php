<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Constraint;

use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;
use Stadly\Date\Interval;

final class Date
{
    /**
     * @var DateInterval Minimum time from then until now.
     */
    private $min;

    /**
     * @var DateInterval|null Maximum time from then until now.
     */
    private $max;

    /**
     * @var int Constraint weight.
     */
    private $weight;

    /**
     * @param DateInterval $min Minimum time from then until now.
     * @param DateInterval|null $max Maximum time from then until now.
     * @param int $weight Constraint weight.
     */
    public function __construct(DateInterval $min, ?DateInterval $max = null, int $weight = 1)
    {
        if (0 < Interval::compare(new DateInterval('PT0S'), $min)) {
            throw new InvalidArgumentException('Min cannot be negative.');
        }
        if ($max !== null && 0 < Interval::compare($min, $max)) {
            throw new InvalidArgumentException('Max cannot be smaller than min.');
        }

        $this->min = $min;
        $this->max = $max;
        $this->weight = $weight;
    }

    /**
     * @return DateInterval Minimum time from then until now.
     */
    public function getMin(): DateInterval
    {
        return $this->min;
    }

    /**
     * @return DateInterval|null Maximum time from then until now.
     */
    public function getMax(): ?DateInterval
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
        $now = new DateTimeImmutable();
        if ($now->sub($this->min) < $date) {
            return false;
        }

        if ($this->max !== null && $date < $now->sub($this->max)) {
            return false;
        }

        return true;
    }
}
