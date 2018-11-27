<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use InvalidArgumentException;
use RuntimeException;
use Stadly\PasswordPolice\Policy;
use Stadly\PasswordPolice\WordList\WordListInterface;

final class Dictionary implements RuleInterface
{
    /**
     * @var WordListInterface Word list for the dictionary.
     */
    private $wordList;

    /**
     * @var int Minimum word length.
     */
    private $min;

    /**
     * @var int|null Maximum word length.
     */
    private $max;

    /**
     * @param WordListInterface $wordList Word list for the dictionary.
     * @param int $min Minimum word length.
     * @param int|null $max Maximum word length.
     */
    public function __construct(WordListInterface $wordList, int $min = 3, ?int $max = 25)
    {
        if ($min < 1) {
            throw new InvalidArgumentException('Min must be positive.');
        }
        if ($max !== null && $max < $min) {
            throw new InvalidArgumentException('Max cannot be smaller than min.');
        }

        $this->wordList = $wordList;
        $this->min = $min;
        $this->max = $max;
    }

    /**
     * @return WordListInterface Word list for the dictionary.
     */
    public function getWordList(): WordListInterface
    {
        return $this->wordList;
    }

    /**
     * @return int Minimum word length.
     */
    public function getMin(): int
    {
        return $this->min;
    }

    /**
     * @return int|null Maximum word length.
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
        for ($start = 0; $start < mb_strlen($password); ++$start) {
            $word = mb_substr($password, $start, $this->max);

            for ($wordLength = mb_strlen($word); $this->min <= $wordLength; --$wordLength) {
                $word = mb_substr($word, 0, $wordLength);

                try {
                    if ($this->wordList->contains($word)) {
                        return false;
                    }
                } catch (RuntimeException $exception) {
                    throw new TestException($this, 'An error occurred while using the word list.', $exception);
                }
            }
        }
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function enforce(string $password): void
    {
        if (!$this->test($password)) {
            throw new RuleException($this, $this->getMessage());
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getMessage(): string
    {
        $translator = Policy::getTranslator();

        return $translator->trans(
            'Must not contain common dictionary words.'
        );
    }
}
