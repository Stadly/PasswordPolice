<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use RuntimeException;
use Stadly\PasswordPolice\CharTree;
use Stadly\PasswordPolice\Formatter;
use Stadly\PasswordPolice\Formatter\Combiner;
use Stadly\PasswordPolice\Rule;
use Stadly\PasswordPolice\ValidationError;
use Stadly\PasswordPolice\WordList;
use Symfony\Contracts\Translation\LocaleAwareInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class DictionaryRule implements Rule
{
    /**
     * @var WordList Word list for the dictionary.
     */
    private $wordList;

    /**
     * @var Formatter Formatter.
     */
    private $formatter;

    /**
     * @var int Constraint weight.
     */
    private $weight;

    /**
     * @param WordList $wordList Word list for the dictionary.
     * @param array<Formatter> $formatters Formatters.
     * @param int $weight Constraint weight.
     */
    public function __construct(WordList $wordList, array $formatters = [], int $weight = 1)
    {
        $this->wordList = $wordList;
        $this->formatter = new Combiner($formatters);
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
    public function validate($password, TranslatorInterface $translator): ?ValidationError
    {
        $word = $this->getDictionaryWord((string)$password);

        if ($word !== null) {
            return new ValidationError(
                $this->getMessage($word, $translator),
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
     * @throws CouldNotUseRuleException If an error occurred.
     */
    private function getDictionaryWord(string $password): ?string
    {
        foreach ($this->formatter->apply(CharTree::fromString($password)) as $formattedPassword) {
            try {
                if ($this->wordList->contains($formattedPassword)) {
                    return $formattedPassword;
                }
            } catch (RuntimeException $exception) {
                throw new CouldNotUseRuleException(
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
     * @param TranslatorInterface&LocaleAwareInterface $translator Translator for translating messages.
     * @return string Message explaining the violation.
     */
    private function getMessage(string $word, TranslatorInterface $translator): string
    {
        return $translator->trans(
            'The password cannot contain dictionary words.'
        );
    }
}
