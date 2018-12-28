<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Constraint;

use DateInterval as PhpDateInterval;
use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;
use Stadly\Date\Interval;

final class DateInterval
{
    /**
     * @var PhpDateInterval Minimum time from then until now.
     */
    private $min;

    /**
     * @var PhpDateInterval|null Maximum time from then until now.
     */
    private $max;

    /**
     * @var int Constraint weight.
     */
    private $weight;

    /**
     * @param PhpDateInterval $min Minimum time from then until now.
     * @param PhpDateInterval|null $max Maximum time from then until now.
     * @param int $weight Constraint weight.
     */
    public function __construct(PhpDateInterval $min, ?PhpDateInterval $max = null, int $weight = 1)
    {
        if (0 < Interval::compare(new PhpDateInterval('PT0S'), $min)) {
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
     * @return PhpDateInterval Minimum time from then until now.
     */
    public function getMin(): PhpDateInterval
    {
        return $this->min;
    }

    /**
     * @return PhpDateInterval|null Maximum time from then until now.
     */
    public function getMax(): ?PhpDateInterval
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
