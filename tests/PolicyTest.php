<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\LocaleAwareInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Policy
 * @covers ::<private>
 * @covers ::<protected>
 * @covers ::__construct
 */
final class PolicyTest extends TestCase
{
    /**
     * @var MockObject&Rule
     */
    private $satisfiedRule1;

    /**
     * @var MockObject&Rule
     */
    private $satisfiedRule2;

    /**
     * @var MockObject&Rule
     */
    private $unsatisfiedRule;

    protected function setUp(): void
    {
        $this->satisfiedRule1 = $this->createMock(Rule::class);
        $this->satisfiedRule1->method('test')->willReturn(true);

        $this->satisfiedRule2 = $this->createMock(Rule::class);
        $this->satisfiedRule2->method('test')->willReturn(true);

        $this->unsatisfiedRule = $this->createMock(Rule::class);
        $validationError = new ValidationError('foo', '', $this->unsatisfiedRule, 1);
        $this->unsatisfiedRule->method('validate')->willReturn($validationError);
    }

    /**
     * @covers ::addRules
     */
    public function testCanAddZeroRules(): void
    {
        $policy1 = new Policy($this->satisfiedRule1);
        $policy1->addRules();

        $policy2 = new Policy($this->satisfiedRule1);
        self::assertEquals($policy1, $policy2);
    }

    /**
     * @covers ::addRules
     */
    public function testCanAddSingleRule(): void
    {
        $policy1 = new Policy($this->satisfiedRule1);
        $policy1->addRules($this->unsatisfiedRule);

        $policy2 = new Policy($this->satisfiedRule1, $this->unsatisfiedRule);
        self::assertEquals($policy1, $policy2);
    }

    /**
     * @covers ::addRules
     */
    public function testCanAddMultipleRules(): void
    {
        $policy1 = new Policy($this->satisfiedRule1);
        $policy1->addRules($this->unsatisfiedRule, $this->satisfiedRule2);

        $policy2 = new Policy($this->satisfiedRule1, $this->unsatisfiedRule, $this->satisfiedRule2);
        self::assertEquals($policy1, $policy2);
    }

    /**
     * @covers ::test
     */
    public function testPolicyWithZeroRulesIsSatisfied(): void
    {
        $policy = new Policy();

        self::assertTrue($policy->test('foo'));
    }

    /**
     * @covers ::test
     */
    public function testPolicyWithSingleRuleCanBeSatisfied(): void
    {
        $policy = new Policy($this->satisfiedRule1);

        self::assertTrue($policy->test('foo'));
    }

    /**
     * @covers ::test
     */
    public function testPolicyWithSingleRuleCanBeUnsatisfied(): void
    {
        $policy = new Policy($this->unsatisfiedRule);

        self::assertFalse($policy->test('foo'));
    }

    /**
     * @covers ::test
     */
    public function testPolicyWithMultipleRulesCanBeSatisfied(): void
    {
        $policy = new Policy($this->satisfiedRule1, $this->satisfiedRule2);

        self::assertTrue($policy->test('foo 159'));
    }

    /**
     * @covers ::test
     */
    public function testPolicyWithMultipleRulesCanBeUnsatisfied(): void
    {
        $policy = new Policy($this->satisfiedRule1, $this->unsatisfiedRule);

        self::assertFalse($policy->test('foo 1'));
    }

    /**
     * @covers ::test
     */
    public function testPolicyIsSatisfiedWhenConstraintWeightIsLowerThanTestWeight(): void
    {
        $policy = new Policy($this->unsatisfiedRule);

        self::assertFalse($policy->test('foo', 2));
    }

    /**
     * @covers ::validate
     */
    public function testPolicyCanBeValidated(): void
    {
        $policy = new Policy($this->satisfiedRule1);

        self::assertEquals([
        ], $policy->validate('foo'), '', 0, 10, true);
    }

    /**
     * @covers ::validate
     */
    public function testPolicyCanBeInvalidated(): void
    {
        $policy = new Policy($this->unsatisfiedRule);

        self::assertEquals([
            new ValidationError('foo', '', $this->unsatisfiedRule, 1),
        ], $policy->validate(''), '', 0, 10, /*Canonicalization does not work*/false);
    }

    /**
     * @covers ::setTranslator
     * @covers ::getTranslator
     */
    public function testCanSetAndGetTranslator(): void
    {
        $policy = new Policy();

        /**
         * @var MockObject&TranslatorInterface&LocaleAwareInterface
         */
        $translator = $this->createMock([TranslatorInterface::class, LocaleAwareInterface::class]);

        $policy->setTranslator($translator);
        self::assertSame($translator, $policy->getTranslator());
        $policy->setTranslator(null);
    }

    /**
     * @covers ::setTranslator
     * @covers ::getTranslator
     */
    public function testCanGetWhenNoTranslatorIsSet(): void
    {
        $policy = new Policy();

        /**
         * @var MockObject&TranslatorInterface&LocaleAwareInterface
         */
        $translator = $this->createMock([TranslatorInterface::class, LocaleAwareInterface::class]);

        $policy->setTranslator($translator);
        $policy->setTranslator(null);

        self::assertNotSame($translator, $policy->getTranslator());
    }
}
