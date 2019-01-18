<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordList;

use ErrorException;
use InvalidArgumentException;
use RuntimeException;
use Stadly\PasswordPolice\WordFormatter;
use Stadly\PasswordPolice\WordList;
use Traversable;

final class Pspell implements WordList
{
    /**
     * @var int Pspell dictionary.
     */
    private $pspell;

    /**
     * @var WordFormatter[] Word formatters.
     */
    private $wordFormatters;

    /**
     * Pspell dictionaries are case-sensitive.
     * Specify word formatters if tests should also be performed for the word formatted to other cases.
     *
     * @param int $pspell Pspell dictionary link, as generated by `pspell_new` and friends.
     * @param WordFormatter ...$wordFormatters Word formatters.
     */
    public function __construct(int $pspell, WordFormatter ...$wordFormatters)
    {
        $this->pspell = $pspell;
        $this->wordFormatters = $wordFormatters;
    }

    /**
     * Pspell dictionaries are case-sensitive.
     * Specify word formatters if tests should also be performed for the word formatted to other cases.
     *
     * @param string $locale Locale of the pspell dictionary to load. For example `en-US` or `de`.
     * @param WordFormatter ...$wordFormatters Word formatters.
     * @throws RuntimeException If the pspell dictionary could not be loaded.
     * @return self Pspell word list.
     */
    public static function fromLocale(string $locale, WordFormatter ...$wordFormatters): self
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

        return new self($pspell, ...$wordFormatters);
    }

    /**
     * {@inheritDoc}
     */
    public function contains(string $word): bool
    {
        foreach ($this->getFormattedWords($word) as $wordVariant) {
            set_error_handler([self::class, 'errorHandler']);
            try {
                $check = pspell_check($this->pspell, $wordVariant);
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
     * @throws ErrorException Error converted to an exception.
     */
    private static function errorHandler(int $severity, string $message, string $filename, int $line): void
    {
        throw new ErrorException($message, /*code*/0, $severity, $filename, $line);
    }
}
