<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use InvalidArgumentException;
use RuntimeException;
use Traversable;
use Stadly\PasswordPolice\Policy;
use Stadly\PasswordPolice\WordConverter\WordConverterInterface;
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
     * @var WordConverterInterface[] Word converters.
     */
    private $wordConverters;

    /**
     * @var int Constraint weight.
     */
    private $weight;

    /**
     * @param WordListInterface $wordList Word list for the dictionary.
     * @param int $minWordLength Ignore words shorter than this.
     * @param int|null $maxWordLength Ignore words longer than this.
     * @param bool $checkSubstrings Check all substrings of the password, not just the whole password.
     * @param WordConverterInterface[] $wordConverters Word converters.
     * @param int $weight Constraint weight.
     */
    public function __construct(
        WordListInterface $wordList,
        int $minWordLength = 3,
        ?int $maxWordLength = 25,
        bool $checkSubstrings = true,
        array $wordConverters = [],
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
        $this->checkSubstrings = $checkSubstrings;
        $this->wordConverters = $wordConverters;
        $this->weight = $weight;
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
    public function enforce($password): void
    {
        $word = $this->getDictionaryWord((string)$password);

        if ($word !== null) {
            throw new RuleException($this, $this->weight, $this->getMessage($word));
        }
    }

    /**
     * @param string $password Password to find dictionary words in.
     * @return string|null Dictionary word in the password.
     * @throws TestException If an error occurred while using the word list.
     */
    private function getDictionaryWord(string $password): ?string
    {
        foreach ($this->getWordsToCheck($password) as $word) {
            try {
                if ($this->wordList->contains($word)) {
                    return $word;
                }
            } catch (RuntimeException $exception) {
                throw new TestException(
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
        $convertedWords = $this->getUniqueWords($this->getConvertedWords($word));

        if ($this->checkSubstrings) {
            $wordsToCheck = $this->getUniqueWords($this->getSubstringWordsToCheck($convertedWords));
        } else {
            $wordsToCheck = $this->getWholeWordsToCheck($convertedWords);
        }

        yield from $wordsToCheck;
    }

    /**
     * @param Traversable<string> $words Words to filter.
     * @return Traversable<string> Unique words.
     */
    private function getUniqueWords(Traversable $words): Traversable
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
     * @param string $word Word to convert.
     * @return Traversable<string> Converted words. May contain duplicates.
     */
    private function getConvertedWords(string $word): Traversable
    {
        yield $word;

        foreach ($this->wordConverters as $wordConverter) {
            foreach ($wordConverter->convert($word) as $converted) {
                yield $converted;
            }
        }
    }

    /**
     * @param Traversable<string> $words Words to check.
     * @return Traversable<string> Whole words to check.
     */
    private function getWholeWordsToCheck(Traversable $words): Traversable
    {
        foreach ($words as $word) {
            if ($this->minWordLength <= mb_strlen($word) &&
               ($this->maxWordLength === null || mb_strlen($word) <= $this->maxWordLength)
            ) {
                yield $word;
            }
        }
    }

    /**
     * @param Traversable<string> $words Words to check.
     * @return Traversable<string> Substring words to check.
     */
    private function getSubstringWordsToCheck(Traversable $words): Traversable
    {
        foreach ($words as $word) {
            for ($start = 0; $start < mb_strlen($word); ++$start) {
                $substring = mb_substr($word, $start, $this->maxWordLength);

                for ($wordLength = mb_strlen($substring); $this->minWordLength <= $wordLength; --$wordLength) {
                    yield mb_substr($substring, 0, $wordLength);
                }
            }
        }
    }

    /**
     * @param string $word Word that violates the constraint.
     * @return string Message explaining the violation.
     */
    private function getMessage(string $word): string
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
