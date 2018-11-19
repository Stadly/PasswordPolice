<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use InvalidArgumentException;
use Symfony\Component\Translation\Translator;

final class Length implements RuleInterface
{
    /**
     * @var int Minimum password length.
     */
    private $min;

    /**
     * @var int|null Maximum password length.
     */
    private $max;

    /**
     * @param int $min Minimum password length.
     * @param int|null $max Maximum password length.
     */
    public function __construct(int $min = 8, ?int $max = null)
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

    /**
     * @return int Minimum password length.
     */
    public function getMin(): int
    {
        return $this->min;
    }

    /**
     * @return int|null Maximum password length.
     */
    public function getMax(): ?int
    {
        return $this->max;
    }

    /**
     * {@inheritDoc}
     */
    public function test(string $password): bool
    {
        $count = $this->getCount($password);

        if ($count < $this->min) {
            return false;
        }

        if (null !== $this->max && $this->max < $count) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function enforce(string $password, Translator $translator): void
    {
        if (!$this->test($password)) {
            throw new RuleException($this, $this->getMessage($translator));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getMessage(Translator $translator): string
    {
        if ($this->getMax() === null) {
            return $translator->transChoice(
                'There must be at least one character.|'.
                'There must be at least %count% characters.',
                $this->getMin()
            );
        }

        if ($this->getMax() === 0) {
            return $translator->trans(
                'There must be no characters.'
            );
        }

        if ($this->getMin() === 0) {
            return $translator->transChoice(
                'There must be at most one character.|'.
                'There must be at most %count% characters.',
                $this->getMax()
            );
        }

        if ($this->getMin() === $this->getMax()) {
            return $translator->transChoice(
                'There must be exactly one character.|'.
                'There must be exactly %count% characters.',
                $this->getMin()
            );
        }

        return $translator->trans(
            'There must be between %min% and %max% characters.',
            ['%min%' => $this->getMin(), '%max%' => $this->getMax()]
        );
    }

    /**
     * @param string $password Password to count characters in.
     * @return int Number of characters.
     */
    private function getCount(string $password): int
    {
        return mb_strlen($password);
    }
}
