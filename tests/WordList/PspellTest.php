<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordList;

use InvalidArgumentException;
use PHPUnit\Framework\Error\Notice;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Stadly\PasswordPolice\CharTree;
use Stadly\PasswordPolice\Formatter;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\WordList\Pspell
 * @covers ::<private>
 * @covers ::<protected>
 * @covers ::__construct
 * @covers ::fromLocale
 */
final class PspellTest extends TestCase
{
    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructWordListFromPspellDirectoryLink(): void
    {
        $dictionary = pspell_new('en');
        assert($dictionary !== false);

        new Pspell($dictionary);
    }

    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructWordListFromInvalidPspellDirectoryLink(): void
    {
        new Pspell(-1);
    }

    /**
     * @covers ::fromLocale
     * @doesNotPerformAssertions
     */
    public function testCanConstructWordListFromValidLocale(): void
    {
        Pspell::fromLocale('en');
    }

    /**
     * @covers ::fromLocale
     */
    public function testCannotConstructWordListFromEmptyLocale(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Pspell::fromLocale('');
    }

    /**
     * @covers ::fromLocale
     */
    public function testCannotConstructWordListFromInvalidLocale(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Pspell::fromLocale('foo');
    }

    /**
     * @covers ::fromLocale
     */
    public function testCannotConstructWordListFromNonExistingLocale(): void
    {
        $this->expectException(RuntimeException::class);

        Pspell::fromLocale('zz-ZZ');
    }

    /**
     * @covers ::contains
     */
    public function testErrorHandlerIsRestoredWhenConstructFromLocaleSucceeds(): void
    {
        Pspell::fromLocale('en');

        $this->expectException(Notice::class);

        trigger_error('foo');
    }

    /**
     * @covers ::contains
     */
    public function testErrorHandlerIsRestoredWhenConstructFromLocaleFails(): void
    {
        try {
            Pspell::fromLocale('zz-ZZ');
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
    public function testWordListCanContainWordsAfterSingleFormatter(): void
    {
        $formatter = $this->createMock(Formatter::class);
        $formatter->method('apply')->willReturnCallback(
            static function (CharTree $charTree): CharTree {
                $charTrees = [];
                foreach ($charTree as $string) {
                    $charTrees[] = CharTree::fromString(mb_strtolower($string));
                }
                return CharTree::fromArray($charTrees);
            }
        );

        $pspell = Pspell::fromLocale('en', [$formatter]);

        self::assertTrue($pspell->contains('HUSband'));
        self::assertFalse($pspell->contains('Usa'));
        self::assertFalse($pspell->contains('europe'));
        self::assertFalse($pspell->contains('iPHONE'));
    }

    /**
     * @covers ::contains
     */
    public function testWordListCanContainWordsAfterMultipleFormatters(): void
    {
        $formatter1 = $this->createMock(Formatter::class);
        $formatter1->method('apply')->willReturnCallback(
            static function (CharTree $charTree): CharTree {
                $charTrees = [];
                foreach ($charTree as $string) {
                    $charTrees[] = CharTree::fromString(mb_strtolower($string));
                }
                return CharTree::fromArray($charTrees);
            }
        );

        $formatter2 = $this->createMock(Formatter::class);
        $formatter2->method('apply')->willReturnCallback(
            static function (CharTree $charTree): CharTree {
                $charTrees = [];
                foreach ($charTree as $string) {
                    $charTrees[] = CharTree::fromString(mb_strtoupper($string));
                }
                return CharTree::fromArray($charTrees);
            }
        );

        $pspell = Pspell::fromLocale('en', [$formatter1, $formatter2]);

        self::assertTrue($pspell->contains('HUSband'));
        self::assertTrue($pspell->contains('Usa'));
        self::assertFalse($pspell->contains('europe'));
        self::assertFalse($pspell->contains('iPHONE'));
    }

    /**
     * @covers ::contains
     */
    public function testWordListCanContainUnformattedWordsAfterFormatter(): void
    {
        $formatter = $this->createMock(Formatter::class);
        $formatter->method('apply')->willReturnCallback(
            static function (CharTree $charTree): CharTree {
                $charTrees = [];
                foreach ($charTree as $string) {
                    $charTrees[] = CharTree::fromString(mb_strtolower($string));
                }
                return CharTree::fromArray($charTrees);
            }
        );

        $pspell = Pspell::fromLocale('en', [$formatter]);

        self::assertTrue($pspell->contains('HUSband'));
        self::assertTrue($pspell->contains('husband'));
        self::assertFalse($pspell->contains('Usa'));
        self::assertTrue($pspell->contains('USA'));
        self::assertFalse($pspell->contains('europe'));
        self::assertTrue($pspell->contains('Europe'));
        self::assertFalse($pspell->contains('iPHONE'));
        self::assertTrue($pspell->contains('iPhone'));
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
