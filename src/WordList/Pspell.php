<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordList;

use ErrorException;
use InvalidArgumentException;
use RuntimeException;
use Stadly\PasswordPolice\CharTree;
use Stadly\PasswordPolice\Formatter;
use Stadly\PasswordPolice\Formatter\Combiner;
use Stadly\PasswordPolice\WordList;

final class Pspell implements WordList
{
    /**
     * @var int Pspell dictionary.
     */
    private $pspell;

    /**
     * @var Formatter Formatter.
     */
    private $formatter;

    /**
     * Pspell dictionaries are case-sensitive.
     * Specify case formatters if tests should also be performed for the word formatted to other cases.
     *
     * @param int $pspell Pspell dictionary link, as generated by `pspell_new` and friends.
     * @param Formatter[] $formatters Formatters.
     */
    public function __construct(int $pspell, array $formatters = [])
    {
        $this->pspell = $pspell;
        $this->formatter = new Combiner($formatters);
    }

    /**
     * Pspell dictionaries are case-sensitive.
     * Specify case formatters if tests should also be performed for the word formatted to other cases.
     *
     * @param string $locale Locale of the pspell dictionary to load. For example `en-US` or `de`.
     * @param Formatter[] $formatters Formatters.
     * @throws RuntimeException If the pspell dictionary could not be loaded.
     * @return self Pspell word list.
     */
    public static function fromLocale(string $locale, array $formatters = []): self
    {
        if (preg_match('{^[a-z]{2}(?:[-_][A-Z]{2})?$}', $locale) !== 1) {
            throw new InvalidArgumentException(sprintf('%s is not a valid locale.', $locale));
        }

        set_error_handler([self::class, 'errorHandler']);
        try {
            $pspell = pspell_new($locale);
        } catch (ErrorException $exception) {
            throw new RuntimeException(
                'An error occurred while loading the word list: '.$exception->getMessage(),
                /*code*/0,
                $exception
            );
        } finally {
            restore_error_handler();
        }

        assert($pspell !== false);

        return new self($pspell, $formatters);
    }

    /**
     * {@inheritDoc}
     */
    public function contains(string $word): bool
    {
        foreach ($this->formatter->apply(CharTree::fromString($word)) as $formattedWord) {
            set_error_handler([self::class, 'errorHandler']);
            try {
                $check = pspell_check($this->pspell, $formattedWord);
            } catch (ErrorException $exception) {
                throw new RuntimeException(
                    'An error occurred while using the word list: '.$exception->getMessage(),
                    /*code*/0,
                    $exception
                );
            } finally {
                restore_error_handler();
            }

            if ($check) {
                return true;
            }
        }

        return false;
    }

    /**
     * @throws ErrorException Error converted to an exception.
     */
    private static function errorHandler(int $severity, string $message, string $filename, int $line): bool
    {
        throw new ErrorException($message, /*code*/0, $severity, $filename, $line);
    }
}
