<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\Rule;
use Stadly\PasswordPolice\ValidationError;
use Symfony\Component\Translation\Translator;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Rule\ConditionalRule
 * @covers ::<private>
 * @covers ::<protected>
 * @covers ::__construct
 */
final class ConditionalRuleTest extends TestCase
{
    /**
     * @var MockObject&Rule
     */
    private $rule;

    protected function setUp(): void
    {
        $this->rule = $this->createMock(Rule::class);
        $this->rule->method('test')->willReturn(false);
        $validationError = new ValidationError('bar', '', $this->rule, 1);
        $this->rule->method('validate')->willReturn($validationError);
    }

    /**
     * @covers ::test
     */
    public function testRuleIsNotTestedWhenConditionIsFalse(): void
    {
        $rule = new ConditionalRule(
            $this->rule,
            static function ($password): bool {
                return false;
            }
        );

        $this->rule->expects(self::never())->method('test');

        self::assertTrue($rule->test('foo'));
    }

    /**
     * @covers ::test
     */
    public function testRuleIsTestedWhenConditionIsTrue(): void
    {
        $rule = new ConditionalRule(
            $this->rule,
            static function ($password): bool {
                return true;
            }
        );

        $this->rule->expects(self::once())->method('test');

        self::assertFalse($rule->test('foo'));
    }

    /**
     * @covers ::validate
     */
    public function testRuleIsNotValidatedWhenConditionIsFalse(): void
    {
        $rule = new ConditionalRule(
            $this->rule,
            static function ($password): bool {
                return false;
            }
        );

        $this->rule->expects(self::never())->method('validate');

        self::assertNull($rule->validate('foo', new Translator('en_US')));
    }

    /**
     * @covers ::validate
     */
    public function testRuleIsValidatedWhenConditionIsTrue(): void
    {
        $rule = new ConditionalRule(
            $this->rule,
            static function ($password): bool {
                return true;
            }
        );

        $this->rule->expects(self::once())->method('validate');

        self::assertEquals(
            new ValidationError('bar', '', $this->rule, 1),
            $rule->validate('foo', new Translator('en_US'))
        );
    }
}
