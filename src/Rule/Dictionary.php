<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use InvalidArgumentException;
use RuntimeException;
use Stadly\PasswordPolice\Policy;
use Stadly\PasswordPolice\Rule;
use Stadly\PasswordPolice\ValidationError;
use Stadly\PasswordPolice\WordFormatter;
use Stadly\PasswordPolice\WordList;
use Traversable;

final class Dictionary implements Rule
{
    /**
     * @var WordList Word list for the dictionary.
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
     * @var WordFormatter[] Word formatters.
     */
    private $wordFormatters;

    /**
     * @var int Constraint weight.
     */
    private $weight;

    /**
     * @param WordList $wordList Word list for the dictionary.
     * @param int $minWordLength Ignore words shorter than this.
     * @param int|null $maxWordLength Ignore words longer than this.
     * @param WordFormatter[] $wordFormatters Word formatters.
     * @param int $weight Constraint weight.
     */
    public function __construct(
        WordList $wordList,
        int $minWordLength = 3,
        ?int $maxWordLength = 25,
        array $wordFormatters = [],
        int $weight = 1
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
        $this->wordFormatters = $wordFormatters;
        $this->weight = $weight;
    }

    /**
     * @return WordList Word list for the dictionary.
     */
    public function getWordList(): WordList
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
    public function test($password, ?int $weight = 1): bool
    {
        if ($weight !== null && $this->weight < $weight) {
            return true;
        }

        $word = $this->getDictionaryWord((string)$password);

        return $word === null;
    }

    /**
     * {@inheritDoc}
     */
    public function validate($password): ?ValidationError
    {
        $word = $this->getDictionaryWord((string)$password);

        if ($word !== null) {
            return new ValidationError(
                $this->getMessage($word),
                $password,
                $this,
                $this->weight
            );
        }

        return null;
    }

    /**
     * @param string $password Password to find dictionary words in.
     * @return string|null Dictionary word in the password.
     * @throws Exception If an error occurred.
     */
    private function getDictionaryWord(string $password): ?string
    {
        foreach ($this->getWordsToCheck($password) as $word) {
            try {
                if ($this->wordList->contains($word)) {
                    return $word;
                }
            } catch (RuntimeException $exception) {
                throw new Exception(
                    $this,
                    'An error occurred while using the word list: '.$exception->getMessage(),
                    $exception
                );
            }
        }
        return null;
    }

    /**
     * @param string $word Word to check.
     * @return Traversable<string> Variants of the word to check.
     */
    private function getWordsToCheck(string $word): Traversable
    {
        $formattedWords = $this->getUniqueWords($this->getFormattedWords($word));

        foreach ($formattedWords as $formattedWord) {
            if ($this->minWordLength <= mb_strlen($formattedWord) &&
               ($this->maxWordLength === null || mb_strlen($formattedWord) <= $this->maxWordLength)
            ) {
                yield $formattedWord;
            }
        }
    }

    /**
     * @param iterable<string> $words Words to filter.
     * @return Traversable<string> Unique words.
     */
    private function getUniqueWords(iterable $words): Traversable
    {
        $checked = [];
        foreach ($words as $word) {
            if (isset($checked[$word])) {
                continue;
            }

            $checked[$word] = true;
            yield $word;
        }
    }

    /**
     * @param string $word Word to format.
     * @return Traversable<string> Formatted words. May contain duplicates.
     */
    private function getFormattedWords(string $word): Traversable
    {
        yield $word;

        foreach ($this->wordFormatters as $wordFormatter) {
            yield from $wordFormatter->apply([$word]);
        }
    }

    /**
     * @param string $word Word that violates the constraint.
     * @return string Message explaining the violation.
     */
    private function getMessage(string $word): string
    {
        $translator = Policy::getTranslator();

        return $translator->trans(
            'Must not contain dictionary words.'
        );
    }
}
