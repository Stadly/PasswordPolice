<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\LocaleAwareInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Policy
 * @covers ::<protected>
 * @covers ::<private>
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

    /**
     * @var MockObject&TranslatorInterface&LocaleAwareInterface
     */
    private $translator;

    protected function setUp(): void
    {
        $this->satisfiedRule1 = $this->createMock(Rule::class);
        $this->satisfiedRule1->method('test')->willReturn(true);

        $this->satisfiedRule2 = $this->createMock(Rule::class);
        $this->satisfiedRule2->method('test')->willReturn(true);

        $this->unsatisfiedRule = $this->createMock(Rule::class);
        $validationError = new ValidationError('foo', '', $this->unsatisfiedRule, 1);
        $this->unsatisfiedRule->method('validate')->willReturn($validationError);

        /**
         * @var MockObject&TranslatorInterface&LocaleAwareInterface
         */
        $translator = $this->createMock([TranslatorInterface::class, LocaleAwareInterface::class]);
        $this->translator = $translator;
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructPolicyWithZeroRules(): void
    {
        $policy = new Policy();

        // Force generation of code coverage
        $policyConstruct = new Policy();
        self::assertEquals($policy, $policyConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructPolicyWithSingleRule(): void
    {
        $policy = new Policy($this->satisfiedRule1);

        // Force generation of code coverage
        $policyConstruct = new Policy($this->satisfiedRule1);
        self::assertEquals($policy, $policyConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructPolicyWithMultipleRules(): void
    {
        $policy = new Policy($this->satisfiedRule1, $this->unsatisfiedRule);

        // Force generation of code coverage
        $policyConstruct = new Policy($this->satisfiedRule1, $this->unsatisfiedRule);
        self::assertEquals($policy, $policyConstruct);
    }

    /**
     * @covers ::addRules
     */
    public function testCanAddZeroRules(): void
    {
        $policy = new Policy($this->satisfiedRule1);
        $policy->addRules();

        $policyConstruct = new Policy($this->satisfiedRule1);
        self::assertEquals($policy, $policyConstruct);
    }

    /**
     * @covers ::addRules
     */
    public function testCanAddSingleRule(): void
    {
        $policy = new Policy($this->satisfiedRule1);
        $policy->addRules($this->unsatisfiedRule);

        $policyConstruct = new Policy($this->satisfiedRule1, $this->unsatisfiedRule);
        self::assertEquals($policy, $policyConstruct);
    }

    /**
     * @covers ::addRules
     */
    public function testCanAddMultipleRules(): void
    {
        $policy = new Policy($this->satisfiedRule1);
        $policy->addRules($this->unsatisfiedRule, $this->satisfiedRule2);

        $policyConstruct = new Policy($this->satisfiedRule1, $this->unsatisfiedRule, $this->satisfiedRule2);
        self::assertEquals($policy, $policyConstruct);
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
     * @covers ::validate
     */
    public function testPolicyCanBeValidated(): void
    {
        $policy = new Policy($this->satisfiedRule1);

        self::assertSame([], $policy->validate('foo'));
    }

    /**
     * @covers ::validate
     */
    public function testPolicyCanBeInvalidated(): void
    {
        $policy = new Policy($this->unsatisfiedRule);

        self::assertEquals(
            [new ValidationError('foo', '', $this->unsatisfiedRule, 1)],
            $policy->validate('')
        );
    }

    /**
     * @covers ::setTranslator
     * @covers ::getTranslator
     */
    public function testCanSetAndGetTranslator(): void
    {
        Policy::setTranslator($this->translator);
        self::assertSame($this->translator, Policy::getTranslator());
        Policy::setTranslator(null);
    }

    /**
     * @covers ::setTranslator
     * @covers ::getTranslator
     */
    public function testCanGetWhenNoTranslatorIsSet(): void
    {
        Policy::setTranslator($this->translator);
        Policy::setTranslator(null);

        self::assertNotSame($this->translator, Policy::getTranslator());
    }
}
