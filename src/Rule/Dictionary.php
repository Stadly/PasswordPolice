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
     * @var int Minimum word length to consider.
     */
    private $minWordLength;

    /**
     * @var int|null Maximum word length to consider.
     */
    private $maxWordLength;

    /**
     * @param WordListInterface $wordList Word list for the dictionary.
     * @param int $minWordLength Ignore words shorter than this.
     * @param int|null $maxWordLength Ignore words longer than this.
     */
    public function __construct(WordListInterface $wordList, int $minWordLength = 3, ?int $maxWordLength = 25)
    {
        if ($minWordLength < 1) {
            throw new InvalidArgumentException('Minimum word length must be positive.');
        }
        if ($maxWordLength !== null && $maxWordLength < $minWordLength) {
            throw new InvalidArgumentException('Maximum word length cannot be smaller than mininum word length.');
        }

        $this->wordList = $wordList;
        $this->minWordLength = $minWordLength;
        $this->maxWordLength = $maxWordLength;
    }

    /**
     * @return WordListInterface Word list for the dictionary.
     */
    public function getWordList(): WordListInterface
    {
        return $this->wordList;
    }

    /**
     * @return int Minimum word length to consider.
     */
    public function getMinWordLength(): int
    {
        return $this->minWordLength;
    }

    /**
     * @return int|null Maximum word length to consider.
     */
    public function getMaxWordLength(): ?int
    {
        return $this->maxWordLength;
    }

    /**
     * {@inheritDoc}
     */
    public function test($password): bool
    {
        $word = $this->getDictionaryWord((string)$password);

        return $word === null;
    }

    /**
     * {@inheritDoc}
     */
    public function enforce($password): void
    {
        $word = $this->getDictionaryWord((string)$password);

        if ($word !== null) {
            throw new RuleException($this, $this->getMessage());
        }
    }

    /**
     * @param string $password Password to find dictionary words in.
     * @return string|null Dictionary word in the password.
     * @throws TestException If an error occurred while using the word list.
     */
    private function getDictionaryWord(string $password): ?string
    {
        for ($start = 0; $start < mb_strlen($password); ++$start) {
            $word = mb_substr($password, $start, $this->maxWordLength);

            for ($wordLength = mb_strlen($word); $this->minWordLength <= $wordLength; --$wordLength) {
                $word = mb_substr($word, 0, $wordLength);

                try {
                    if ($this->wordList->contains($word)) {
                        return $word;
                    }
                } catch (RuntimeException $exception) {
                    throw new TestException($this, 'An error occurred while using the word list.', $exception);
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
            'Must not contain common dictionary words.'
        );
    }
}
