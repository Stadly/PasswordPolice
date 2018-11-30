<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use DateTimeInterface;
use InvalidArgumentException;
use RuntimeException;
use Stadly\PasswordPolice\FormerPassword;
use Stadly\PasswordPolice\Password;
use Stadly\PasswordPolice\Policy;
use Stadly\PasswordPolice\HashFunction\HashFunctionInterface;
use Stadly\PasswordPolice\WordList\WordListInterface;

final class NoReuse implements RuleInterface
{
    /**
     * @var HashFunctionInterface Hash function.
     */
    private $hashFunction;

    /**
     * @var int First former password to consider.
     */
    private $first;

    /**
     * @var int|null Number of former passwords to consider.
     */
    private $count;

    /**
     * @param int|null $count Number of former passwords to consider.
     * @param int $first First former password to consider.
     */
    public function __construct(HashFunctionInterface $hashFunction, ?int $count = null, int $first = 1)
    {
        if ($first < 1) {
            throw new InvalidArgumentException('First must be positive.');
        }
        if ($count !== null && $count < 1) {
            throw new InvalidArgumentException('Count must be positive.');
        }

        $this->hashFunction = $hashFunction;
        $this->first = $first;
        $this->count = $count;
    }

    /**
     * @return HashFunctionInterface Hash function.
     */
    public function getHashFunction(): HashFunctionInterface
    {
        return $this->hashFunction;
    }

    /**
     * @return int First former password to consider.
     */
    public function getFirst(): int
    {
        return $this->first;
    }

    /**
     * @return int|null Number of former passwords to consider.
     */
    public function getCount(): ?int
    {
        return $this->count;
    }

    /**
     * Check whether a password is in compliance with the rule.
     *
     * @param Password|string $password Password to check.
     * @return bool Whether the password is in compliance with the rule.
     */
    public function test($password): bool
    {
        $formerPassword = $this->getFormerPassword($password);

        return $formerPassword === null;
    }

    /**
     * Enforce that a password is in compliance with the rule.
     *
     * @param Password|string $password Password that must adhere to the rule.
     * @throws RuleException If the password does not adhrere to the rule.
     */
    public function enforce($password): void
    {
        $formerPassword = $this->getFormerPassword($password);

        if ($formerPassword !== null) {
            throw new RuleException($this, $this->getMessage());
        }
    }

    /**
     * @param Password|string $password Password to compare with former passwords.
     * @return FormerPassword|null Former password matching the password.
     */
    private function getFormerPassword($password): ?FormerPassword
    {
        if ($password instanceof Password) {
            $formerPasswords = $password->getFormerPasswords();

            $start = $this->first-1;
            $end = count($formerPasswords);
            if ($this->count !== null) {
                $end = min($end, $start+$this->count);
            }

            for ($i = $start; $i < $end; ++$i) {
                if ($this->hashFunction->compare((string)$password, (string)$formerPasswords[$i])) {
                    return $formerPasswords[$i];
                }
            }
        }
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function getMessage(): string
    {
        $translator = Policy::getTranslator();

        return $translator->trans(
            'Cannot reuse former passwords.'
        );
    }
}
