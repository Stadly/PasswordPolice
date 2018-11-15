<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice;

use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\Rule\Digit;
use Stadly\PasswordPolice\Rule\Length;
use Stadly\PasswordPolice\Rule\LowerCase;
use Symfony\Component\Translation\Translator;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Policy
 * @covers ::<protected>
 * @covers ::<private>
 */
final class PolicyTest extends TestCase
{
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
        $policy = new Policy(new Length(1));

        // Force generation of code coverage
        $policyConstruct = new Policy(new Length(1));
        self::assertEquals($policy, $policyConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructPolicyWithMultipleRules(): void
    {
        $policy = new Policy(new Length(1), new Digit(2));

        // Force generation of code coverage
        $policyConstruct = new Policy(new Length(1), new Digit(2));
        self::assertEquals($policy, $policyConstruct);
    }

    /**
     * @covers ::addRules
     */
    public function testCanAddZeroRules(): void
    {
        $policy = new Policy(new Length(1));
        $policy->addRules();

        $policyConstruct = new Policy(new Length(1));
        self::assertEquals($policy, $policyConstruct);
    }

    /**
     * @covers ::addRules
     */
    public function testCanAddSingleRule(): void
    {
        $policy = new Policy(new Length(1));
        $policy->addRules(new Digit(2));

        $policyConstruct = new Policy(new Length(1), new Digit(2));
        self::assertEquals($policy, $policyConstruct);
    }

    /**
     * @covers ::addRules
     */
    public function testCanAddMultipleRules(): void
    {
        $policy = new Policy(new Length(1));
        $policy->addRules(new Digit(2), new LowerCase(3));

        $policyConstruct = new Policy(new Length(1), new Digit(2), new LowerCase(3));
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
        $policy = new Policy(new Length(2));

        self::assertTrue($policy->test('foo'));
    }

    /**
     * @covers ::test
     */
    public function testPolicyWithSingleRuleCanBeUnsatisfied(): void
    {
        $policy = new Policy(new Length(4));

        self::assertFalse($policy->test('foo'));
    }

    /**
     * @covers ::test
     */
    public function testPolicyWithMultipleRulesCanBeSatisfied(): void
    {
        $policy = new Policy(new Length(2), new Digit(2));

        self::assertTrue($policy->test('foo 159'));
    }

    /**
     * @covers ::test
     */
    public function testPolicyWithMultipleRulesCanBeUnsatisfied(): void
    {
        $policy = new Policy(new Length(2), new Digit(2));

        self::assertFalse($policy->test('foo 1'));
    }

    /**
     * @covers ::enforce
     */
    public function testEnforceDoesNotThrowExceptionWhenPolicyIsSatisfied(): void
    {
        $policy = new Policy();
        $translator = new Translator('en_US');

        $policy->enforce('foo', $translator);

        // Force generation of code coverage
        $policyConstruct = new Policy();
        self::assertEquals($policy, $policyConstruct);
    }

    /**
     * @covers ::enforce
     */
    public function testEnforceThrowsExceptionWhenPolicyIsNotSatisfied(): void
    {
        $policy = new Policy(new Length(1));
        $translator = new Translator('en_US');

        $this->expectException(PolicyException::class);

        $policy->enforce('', $translator);
    }
}
