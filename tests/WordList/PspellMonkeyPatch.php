<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordList;

/**
 * @return int|false
 */
function pspell_new(
    string $language,
    string $spelling = '',
    string $jargon = '',
    string $encoding = '',
    int $mode = 0
) {
    if ($language === 'en') {
        return 1;
    }

    // Trigger error in the scope of Pspell, so it can be caught by the error handler.
    (static function (): void {
        trigger_error('foo');
    })->bindTo(null, Pspell::class)();

    return false;
}

function pspell_check(int $dictionary_link, string $word): bool
{
    if ($dictionary_link < 0) {
        // Trigger error in the scope of Pspell, so it can be caught by the error handler.
        (static function (): void {
            trigger_error('foo');
        })->bindTo(null, Pspell::class)();
    } else {
        switch ($word) {
            case 'husband':
            case 'USA':
            case 'Europe':
            case 'iPhone':
                return true;
        }
    }

    return false;
}
