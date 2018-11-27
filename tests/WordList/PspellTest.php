<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordList;

use InvalidArgumentException;
use Patchwork\CallRerouting\Handle;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Error\Notice;
use RuntimeException;
use Stadly\PasswordPolice\CaseConverter\CaseConverterInterface;
use function Patchwork\redefine;
use function Patchwork\restore;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\WordList\Pspell
 * @covers ::<protected>
 * @covers ::<private>
 */
final class PspellTest extends TestCase
{
    /**
     * @var Handle Patchwork handle.
     */
    private $pspellNewPatch;

    /**
     * @var Handle Patchwork handle.
     */
    private $pspellCheckPatch;

    protected function setUp(): void
    {
        // Errors are triggered in the scope of Pspell, so they can be caught by the error handler.
        $triggerError = (function () {
            trigger_error('foo');
        })->bindTo(null, Pspell::class);

        $this->pspellNewPatch = redefine(
            'pspell_new',
            function (string $locale) use ($triggerError) {
                if ($locale === 'en') {
                    return 1;
                }

                $triggerError();

                return false;
            }
        );

        $this->pspellCheckPatch = redefine(
            'pspell_check',
            function (int $pspell, string $word) use ($triggerError): bool {
                if ($pspell < 0) {
                    $triggerError();
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
        );
    }

    protected function tearDown(): void
    {
        restore($this->pspellNewPatch);
        restore($this->pspellCheckPatch);
    }

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
    public function testWordListCanContainWordsAfterSingleCaseConverter(): void
    {
        $caseConverter = $this->createMock(CaseConverterInterface::class);
        $caseConverter->method('convert')->willReturnCallback('mb_strtolower');

        $pspell = Pspell::fromLocale('en', $caseConverter);

        self::assertTrue($pspell->contains('HUSband'));
        self::assertFalse($pspell->contains('Usa'));
        self::assertFalse($pspell->contains('europe'));
        self::assertFalse($pspell->contains('iPHONE'));
    }

    /**
     * @covers ::contains
     */
    public function testWordListCanContainWordsAfterMultipleCaseConverters(): void
    {
        $caseConverter1 = $this->createMock(CaseConverterInterface::class);
        $caseConverter1->method('convert')->willReturnCallback('mb_strtolower');

        $caseConverter2 = $this->createMock(CaseConverterInterface::class);
        $caseConverter2->method('convert')->willReturnCallback('mb_strtoupper');

        $pspell = Pspell::fromLocale('en', $caseConverter1, $caseConverter2);

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
