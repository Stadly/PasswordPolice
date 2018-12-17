<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use Http\Factory\Discovery\ClientLocator;
use Http\Factory\Discovery\FactoryLocator;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\Stub\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use RuntimeException;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Rule\HaveIBeenPwned
 * @covers ::<protected>
 * @covers ::<private>
 */
final class HaveIBeenPwnedTest extends TestCase
{
    protected function setUp(): void
    {
        FactoryLocator::register(RequestFactoryInterface::class, MockedRequestFactory::class);
        ClientLocator::register(ClientInterface::class, HaveIBeenPwnedClient::class);
    }

    protected function tearDown(): void
    {
        FactoryLocator::unregister(RequestFactoryInterface::class, MockedRequestFactory::class);
        ClientLocator::unregister(ClientInterface::class, HaveIBeenPwnedClient::class);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithMinConstraint(): void
    {
        $rule = new HaveIBeenPwned(null, 5);

        // Force generation of code coverage
        $ruleConstruct = new HaveIBeenPwned(null, 5);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithMaxConstraint(): void
    {
        $rule = new HaveIBeenPwned(10, 0);

        // Force generation of code coverage
        $ruleConstruct = new HaveIBeenPwned(10, 0);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithBothMinAndMaxConstraint(): void
    {
        $rule = new HaveIBeenPwned(10, 5);

        // Force generation of code coverage
        $ruleConstruct = new HaveIBeenPwned(10, 5);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithNegativeMinConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = new HaveIBeenPwned(null, -10);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithMaxConstraintSmallerThanMinConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = new HaveIBeenPwned(5, 10);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructUnconstrainedRule(): void
    {
        $rule = new HaveIBeenPwned(null, 0);

        // Force generation of code coverage
        $ruleConstruct = new HaveIBeenPwned(null, 0);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithMinConstraintEqualToMaxConstraint(): void
    {
        $rule = new HaveIBeenPwned(5, 5);

        // Force generation of code coverage
        $ruleConstruct = new HaveIBeenPwned(5, 5);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::setClient
     */
    public function testCanSetClient(): void
    {
        $rule = new HaveIBeenPwned(10, 5);
        $ruleConstruct = new HaveIBeenPwned(10, 5);
        self::assertEquals($rule, $ruleConstruct);

        $client = $this->createMock(ClientInterface::class);
        $rule->setClient($client);
        self::assertNotEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::setRequestFactory
     */
    public function testCanSetRequestFactory(): void
    {
        $rule = new HaveIBeenPwned(10, 5);
        $ruleConstruct = new HaveIBeenPwned(10, 5);
        self::assertEquals($rule, $ruleConstruct);

        $requestFactory = $this->createMock(RequestFactoryInterface::class);
        $rule->setRequestFactory($requestFactory);
        self::assertNotEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::getMin
     */
    public function testCanGetMinConstraint(): void
    {
        $rule = new HaveIBeenPwned(10, 5);

        self::assertSame(5, $rule->getMin());
    }

    /**
     * @covers ::getMax
     */
    public function testCanGetMaxConstraint(): void
    {
        $rule = new HaveIBeenPwned(10, 5);

        self::assertSame(10, $rule->getMax());
    }

    /**
     * @covers ::test
     */
    public function testMinConstraintCanBeSatisfied(): void
    {
        $rule = new HaveIBeenPwned(null, 5);

        self::assertTrue($rule->test('6004468405'));
    }

    /**
     * @covers ::test
     */
    public function testMinConstraintCanBeUnsatisfied(): void
    {
        $rule = new HaveIBeenPwned(null, 5);

        self::assertFalse($rule->test('6597812222'));
    }

    /**
     * @covers ::test
     */
    public function testMinConstraintIsNotSatisfiedWhenHashIsNotFound(): void
    {
        $rule = new HaveIBeenPwned(null, 1);

        self::assertFalse($rule->test('291vnnzrvtu9'));
    }

    /**
     * @covers ::test
     */
    public function testMaxConstraintCanBeSatisfied(): void
    {
        $rule = new HaveIBeenPwned(3, 0);

        self::assertTrue($rule->test('553193251'));
    }

    /**
     * @covers ::test
     */
    public function testMaxConstraintCanBeUnsatisfied(): void
    {
        $rule = new HaveIBeenPwned(3, 0);

        self::assertFalse($rule->test('+79250455754'));
    }

    /**
     * @covers ::test
     */
    public function testMaxConstraintIsSatisfiedWhenHashIsNotFound(): void
    {
        $rule = new HaveIBeenPwned(5, 0);

        self::assertTrue($rule->test('291vnnzrvtu9'));
    }

    /**
     * @covers ::test
     */
    public function testErrorsWhenCalculatingCountAreHandled(): void
    {
        $exception = $this->createMock(ClientExceptionInterface::class);
        $stubException = new Exception($exception);

        $client = $this->createMock(ClientInterface::class);
        $client->method('sendRequest')->will($stubException);

        $rule = new HaveIBeenPwned(5, 0);
        $rule->setClient($client);

        $this->expectException(TestException::class);

        $rule->test('291vnnzrvtu9');
    }

    /**
     * @covers ::test
     */
    public function testExceptionIsThrownWhenNoRequestFactoryIsRegistered(): void
    {
        FactoryLocator::unregister(RequestFactoryInterface::class, MockedRequestFactory::class);

        $rule = new HaveIBeenPwned(5, 0);

        $this->expectException(RuntimeException::class);

        $rule->test('291vnnzrvtu9');
    }

    /**
     * @covers ::test
     */
    public function testExceptionIsThrownWhenNoClientIsRegistered(): void
    {
        ClientLocator::unregister(ClientInterface::class, HaveIBeenPwnedClient::class);

        $rule = new HaveIBeenPwned(5, 0);

        $this->expectException(RuntimeException::class);

        $rule->test('291vnnzrvtu9');
    }

    /**
     * @covers ::enforce
     */
    public function testEnforceDoesNotThrowExceptionWhenRuleIsSatisfied(): void
    {
        $client = new HaveIBeenPwnedClient();
        $requestFactory = new MockedRequestFactory();

        $rule = new HaveIBeenPwned(null, 2);
        $rule->setClient($client);
        $rule->setRequestFactory($requestFactory);

        $rule->enforce('1397wpfk');

        // Force generation of code coverage
        $ruleConstruct = new HaveIBeenPwned(null, 2);
        $ruleConstruct->setClient($client);
        $ruleConstruct->setRequestFactory($requestFactory);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::enforce
     */
    public function testEnforceThrowsExceptionWhenRuleIsNotSatisfied(): void
    {
        $rule = new HaveIBeenPwned(null, 3);

        $this->expectException(RuleException::class);

        $rule->enforce('1397wpfk');
    }

    /**
     * @covers ::enforce
     */
    public function testValidationMessageForRuleWithMinConstraint(): void
    {
        $rule = new HaveIBeenPwned(null, 5);

        $this->expectExceptionMessage('Must appear at least 5 times in breaches.');

        $rule->enforce('1397wpfk');
    }

    /**
     * @covers ::enforce
     */
    public function testValidationMessageForRuleWithMaxConstraint(): void
    {
        $rule = new HaveIBeenPwned(10, 0);

        $this->expectExceptionMessage('Must appear at most 10 times in breaches.');

        $rule->enforce('6004468405');
    }

    /**
     * @covers ::enforce
     */
    public function testValidationMessageForRuleWithBothMinAndMaxConstraint(): void
    {
        $rule = new HaveIBeenPwned(10, 5);

        $this->expectExceptionMessage('Must appear between 5 and 10 times in breaches.');

        $rule->enforce('1397wpfk');
    }

    /**
     * @covers ::enforce
     */
    public function testValidationMessageForRuleWithMaxConstraintEqualToZero(): void
    {
        $rule = new HaveIBeenPwned(0, 0);

        $this->expectExceptionMessage('Must not appear in any breaches.');

        $rule->enforce('1397wpfk');
    }

    /**
     * @covers ::enforce
     */
    public function testValidationMessageForRuleWithMinConstraintEqualToMaxConstraint(): void
    {
        $rule = new HaveIBeenPwned(3, 3);

        $this->expectExceptionMessage('Must appear exactly 3 times in breaches.');

        $rule->enforce('1397wpfk');
    }
}
