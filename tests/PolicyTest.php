<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\Rule\RuleException;
use Stadly\PasswordPolice\Rule\RuleInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Policy
 * @covers ::<protected>
 * @covers ::<private>
 */
final class PolicyTest extends TestCase
{
    /**
     * @var MockObject&RuleInterface
     */
    private $satisfiedRule1;

    /**
     * @var MockObject&RuleInterface
     */
    private $satisfiedRule2;

    /**
     * @var MockObject&RuleInterface
     */
    private $unsatisfiedRule;

    protected function setUp(): void
    {
        $this->satisfiedRule1 = $this->createMock(RuleInterface::class);
        $this->satisfiedRule1->method('test')->willReturn(true);

        $this->satisfiedRule2 = $this->createMock(RuleInterface::class);
        $this->satisfiedRule2->method('test')->willReturn(true);

        $ruleException = new RuleException($this->createMock(RuleInterface::class), 'foo');
        $this->unsatisfiedRule = $this->createMock(RuleInterface::class);
        $this->unsatisfiedRule->method('enforce')->willThrowException($ruleException);
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
     * @covers ::enforce
     */
    public function testEnforceDoesNotThrowExceptionWhenPolicyIsSatisfied(): void
    {
        $policy = new Policy($this->satisfiedRule1);

        $policy->enforce('foo');

        // Force generation of code coverage
        $policyConstruct = new Policy($this->satisfiedRule1);
        self::assertEquals($policy, $policyConstruct);
    }

    /**
     * @covers ::enforce
     */
    public function testEnforceThrowsExceptionWhenPolicyIsNotSatisfied(): void
    {
        $policy = new Policy($this->unsatisfiedRule);

        $this->expectException(PolicyException::class);

        $policy->enforce('');
    }

    /**
     * @covers ::setTranslator
     * @covers ::getTranslator
     */
    public function testCanSetAndGetTranslator(): void
    {
        $translator = $this->createMock(TranslatorInterface::class);

        Policy::setTranslator($translator);
        self::assertSame($translator, Policy::getTranslator());
        Policy::setTranslator(null);
    }

    /**
     * @covers ::setTranslator
     * @covers ::getTranslator
     */
    public function testCanGetWhenNoTranslatorIsSet(): void
    {
        $translator = $this->createMock(TranslatorInterface::class);
        Policy::setTranslator($translator);
        Policy::setTranslator(null);

        self::assertNotSame($translator, Policy::getTranslator());
    }
}
