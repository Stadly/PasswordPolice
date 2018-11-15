<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\Translator;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Rule\RuleException
 * @covers ::<protected>
 * @covers ::<private>
 */
final class RuleExceptionTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testCanConstructException(): void
    {
        $exception = new RuleException(new UpperCase(5), 'foo');

        // Force generation of code coverage
        self::assertSame('foo', $exception->getMessage());
    }

    /**
     * @covers ::getRule
     */
    public function testCanGetRule(): void
    {
        $rule = new UpperCase(5);
        $exception = new RuleException($rule, 'foo');

        self::assertSame($rule, $exception->getRule());
    }
}