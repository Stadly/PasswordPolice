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
     * @var bool Whether all substrings of the password should be checked.
     */
    private $checkSubstrings;

    /**
     * @param WordListInterface $wordList Word list for the dictionary.
     * @param int $minWordLength Ignore words shorter than this.
     * @param int|null $maxWordLength Ignore words longer than this.
     * @param bool $checkSubstrings Check all substrings of the password, not just the whole password.
     */
    public function __construct(
        WordListInterface $wordList,
        int $minWordLength = 3,
        ?int $maxWordLength = 25,
        bool $checkSubstrings = true
    ) {
        if ($minWordLength < 1) {
            throw new InvalidArgumentException('Minimum word length must be positive.');
        }
        if ($maxWordLength !== null && $maxWordLength < $minWordLength) {
            throw new InvalidArgumentException('Maximum word length cannot be smaller than mininum word length.');
        }

        $this->wordList = $wordList;
        $this->minWordLength = $minWordLength;
        $this->maxWordLength = $maxWordLength;
        $this->checkSubstrings = $checkSubstrings;
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
        if ($this->checkSubstrings) {
            return $this->getDictionaryWordCheckSubstrings($password);
        }

        return $this->getDictionaryWordCheckWord($password);
    }

    /**
     * @param string $password Password to find dictionary words in.
     * @return string|null Dictionary word in the password.
     * @throws TestException If an error occurred while using the word list.
     */
    private function getDictionaryWordCheckWord(string $password): ?string
    {
        if (mb_strlen($password) < $this->minWordLength) {
            return null;
        }

        if ($this->maxWordLength !== null && $this->maxWordLength < mb_strlen($password)) {
            return null;
        }

        if ($this->wordListContains($password)) {
            return $password;
        }

        return null;
    }

    /**
     * @param string $password Password to find dictionary words in.
     * @return string|null Dictionary word in the password.
     * @throws TestException If an error occurred while using the word list.
     */
    private function getDictionaryWordCheckSubstrings(string $password): ?string
    {
        for ($start = 0; $start < mb_strlen($password); ++$start) {
            $word = mb_substr($password, $start, $this->maxWordLength);

            for ($wordLength = mb_strlen($word); $this->minWordLength <= $wordLength; --$wordLength) {
                $word = mb_substr($word, 0, $wordLength);

                if ($this->wordListContains($word)) {
                    return $word;
                }
            }
        }

        return null;
    }

    /**
     * @param string $word Word to check.
     * @return bool Whether the word list contains the word.
     * @throws TestException If an error occurred while using the word list.
     */
    private function wordListContains(string $word): bool
    {
        try {
            if ($this->wordList->contains($word)) {
                return true;
            }
        } catch (RuntimeException $exception) {
            throw new TestException($this, 'An error occurred while using the word list.', $exception);
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function getMessage(): string
    {
        $translator = Policy::getTranslator();

        if ($this->checkSubstrings) {
            return $translator->trans(
                'Must not contain dictionary words.'
            );
        } else {
            return $translator->trans(
                'Must not be a dictionary word.'
            );
        }
    }
}
