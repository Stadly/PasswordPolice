<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordList;

use ErrorException;
use InvalidArgumentException;
use RuntimeException;
use Traversable;
use Stadly\PasswordPolice\WordConverter\WordConverterInterface;

final class Pspell implements WordListInterface
{
    /**
     * @var int Pspell dictionary.
     */
    private $pspell;

    /**
     * @var WordConverterInterface[] Word converters.
     */
    private $wordConverters;

    /**
     * Pspell dictionaries are case-sensitive.
     * Specify word converters if tests should also be performed for the word converted to other cases.
     *
     * @param int $pspell Pspell dictionary link, as generated by `pspell_new` and friends.
     * @param WordConverterInterface... $wordConverters Word converters.
     */
    public function __construct(int $pspell, WordConverterInterface... $wordConverters)
    {
        $this->pspell = $pspell;
        $this->wordConverters = $wordConverters;
    }

    /**
     * Pspell dictionaries are case-sensitive.
     * Specify word converters if tests should also be performed for the word converted to other cases.
     *
     * @param string $locale Locale of the pspell dictionary to load. For example `en-US` or `de`.
     * @param WordConverterInterface... $wordConverters Word converters.
     * @throws RuntimeException If the pspell dictionary could not be loaded.
     * @return self Pspell word list.
     */
    public static function fromLocale(string $locale, WordConverterInterface... $wordConverters): self
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

        return new self($pspell, ...$wordConverters);
    }

    /**
     * {@inheritDoc}
     */
    public function contains(string $word): bool
    {
        foreach ($this->getWordsToCheck($word) as $wordVariant) {
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
     * @param string $word Word to check.
     * @return Traversable<string> Variants of the word to check.
     */
    private function getWordsToCheck(string $word): Traversable
    {
        $checked = [];
        foreach ($this->getConvertedWords($word) as $wordToCheck) {
            if (isset($checked[$wordToCheck])) {
                continue;
            }

            $checked[$wordToCheck] = true;
            yield $wordToCheck;
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
     * @throws ErrorException Error converted to an exception.
     */
    private static function errorHandler(int $severity, string $message, string $filename, int $line): void
    {
        throw new ErrorException($message, /*code*/0, $severity, $filename, $line);
    }
}
