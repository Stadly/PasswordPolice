<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordList;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Error\Notice;
use RuntimeException;
use Stadly\PasswordPolice\WordConverter;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\WordList\Pspell
 * @covers ::<protected>
 * @covers ::<private>
 */
final class PspellTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testCanConstructWordListFromPspellDirectoryLink(): void
    {
        $dictionary = pspell_new('en');
        assert($dictionary !== false);

        $pspell = new Pspell($dictionary);

        // Force generation of code coverage
        $pspellConstruct = new Pspell($dictionary);
        self::assertEquals($pspell, $pspellConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructWordListFromInvalidPspellDirectoryLink(): void
    {
        $pspell = new Pspell(-1);

        // Force generation of code coverage
        $pspellConstruct = new Pspell(-1);
        self::assertEquals($pspell, $pspellConstruct);
    }

    /**
     * @covers ::fromLocale
     */
    public function testCanConstructWordListFromLocale(): void
    {
        $pspell = Pspell::fromLocale('en');

        // Force generation of code coverage
        $pspellConstruct = Pspell::fromLocale('en');
        self::assertSame(get_class($pspell), get_class($pspellConstruct));
    }

    /**
     * @covers ::fromLocale
     */
    public function testCannotConstructWordListFromEmptyLocale(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $pspell = Pspell::fromLocale('');
    }

    /**
     * @covers ::fromLocale
     */
    public function testCannotConstructWordListFromInvalidLocale(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $pspell = Pspell::fromLocale('foo');
    }

    /**
     * @covers ::fromLocale
     */
    public function testCannotConstructWordListFromNonExistingLocale(): void
    {
        $this->expectException(RuntimeException::class);

        $pspell = Pspell::fromLocale('zz-ZZ');
    }

    /**
     * @covers ::contains
     */
    public function testErrorHandlerIsRestoredWhenConstructFromLocaleSucceeds(): void
    {
        $pspell = Pspell::fromLocale('en');

        $this->expectException(Notice::class);

        trigger_error('foo');
    }

    /**
     * @covers ::contains
     */
    public function testErrorHandlerIsRestoredWhenConstructFromLocaleFails(): void
    {
        try {
            $pspell = Pspell::fromLocale('zz-ZZ');
        } catch (RuntimeException $e) {
            $this->expectException(Notice::class);

            trigger_error('foo');
        }
    }

    /**
     * @covers ::contains
     */
    public function testCannotUseWordListFromInvalidDirectoryLink(): void
    {
        $pspell = new Pspell(-1);

        $this->expectException(RuntimeException::class);

        $pspell->contains('husband');
    }

    /**
     * @covers ::contains
     */
    public function testWordListContainsWordsInCorrectCase(): void
    {
        $pspell = Pspell::fromLocale('en');

        self::assertTrue($pspell->contains('husband'));
        self::assertTrue($pspell->contains('USA'));
        self::assertTrue($pspell->contains('Europe'));
        self::assertTrue($pspell->contains('iPhone'));
    }

    /**
     * @covers ::contains
     */
    public function testWordListDoesNotContainWordsInIncorrectCase(): void
    {
        $pspell = Pspell::fromLocale('en');

        self::assertFalse($pspell->contains('HUSband'));
        self::assertFalse($pspell->contains('Usa'));
        self::assertFalse($pspell->contains('europe'));
        self::assertFalse($pspell->contains('iPHONE'));
    }

    /**
     * @covers ::contains
     */
    public function testWordListCanContainWordsAfterSingleWordConverter(): void
    {
        $wordConverter = $this->createMock(WordConverter::class);
        $wordConverter->method('convert')->willReturnCallback(
            static function ($word) {
                yield mb_strtolower($word);
            }
        );

        $pspell = Pspell::fromLocale('en', $wordConverter);

        self::assertTrue($pspell->contains('HUSband'));
        self::assertFalse($pspell->contains('Usa'));
        self::assertFalse($pspell->contains('europe'));
        self::assertFalse($pspell->contains('iPHONE'));
    }

    /**
     * @covers ::contains
     */
    public function testWordListCanContainWordsAfterMultipleWordConverters(): void
    {
        $wordConverter1 = $this->createMock(WordConverter::class);
        $wordConverter1->method('convert')->willReturnCallback(
            static function ($word) {
                yield mb_strtolower($word);
            }
        );

        $wordConverter2 = $this->createMock(WordConverter::class);
        $wordConverter2->method('convert')->willReturnCallback(
            static function ($word) {
                yield mb_strtoupper($word);
            }
        );

        $pspell = Pspell::fromLocale('en', $wordConverter1, $wordConverter2);

        self::assertTrue($pspell->contains('HUSband'));
        self::assertTrue($pspell->contains('Usa'));
        self::assertFalse($pspell->contains('europe'));
        self::assertFalse($pspell->contains('iPHONE'));
    }

    /**
     * @covers ::contains
     */
    public function testErrorHandlerIsRestoredWhenContainsSucceeds(): void
    {
        $pspell = Pspell::fromLocale('en');
        $pspell->contains('husband');

        $this->expectException(Notice::class);

        trigger_error('foo');
    }

    /**
     * @covers ::contains
     */
    public function testErrorHandlerIsRestoredWhenContainsFails(): void
    {
        $pspell = new Pspell(-1);

        try {
            $pspell->contains('husband');
        } catch (RuntimeException $e) {
            $this->expectException(Notice::class);

            trigger_error('foo');
        }
    }
}
