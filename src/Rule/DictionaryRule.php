<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use RuntimeException;
use Stadly\PasswordPolice\Policy;
use Stadly\PasswordPolice\Rule;
use Stadly\PasswordPolice\ValidationError;
use Stadly\PasswordPolice\WordFormatter;
use Stadly\PasswordPolice\WordFormatter\FormatterCombiner;
use Stadly\PasswordPolice\WordList;
use Traversable;

final class DictionaryRule implements Rule
{
    /**
     * @var WordList Word list for the dictionary.
     */
    private $wordList;

    /**
     * @var WordFormatter Word formatter.
     */
    private $wordFormatter;

    /**
     * @var int Constraint weight.
     */
    private $weight;

    /**
     * @param WordList $wordList Word list for the dictionary.
     * @param WordFormatter[] $wordFormatters Word formatters.
     * @param int $weight Constraint weight.
     */
    public function __construct(WordList $wordList, array $wordFormatters = [], int $weight = 1)
    {
        $this->wordList = $wordList;
        $this->wordFormatter = new FormatterCombiner($wordFormatters);
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
     * @return string|null DictionaryRule word in the password.
     * @throws RuleException If an error occurred.
     */
    private function getDictionaryWord(string $password): ?string
    {
        foreach ($this->wordFormatter->apply([$password]) as $word) {
            try {
                if ($this->wordList->contains($word)) {
                    return $word;
                }
            } catch (RuntimeException $exception) {
                throw new RuleException(
                    $this,
                    'An error occurred while using the word list: '.$exception->getMessage(),
                    $exception
                );
            }
        }
        return null;
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
