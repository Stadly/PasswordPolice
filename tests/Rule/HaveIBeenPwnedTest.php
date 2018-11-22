<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use Http\Discovery\ClassDiscovery;
use Http\Factory\Discovery\FactoryLocator;
use InvalidArgumentException;
use LogicException;
use PHPUnit\Framework\MockObject\Stub\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Symfony\Component\Translation\Translator;

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
        ClassDiscovery::setStrategies([HaveIBeenPwnedDiscoveryStrategy::class]);
    }

    protected function tearDown(): void
    {
        FactoryLocator::unregister(RequestFactoryInterface::class, MockedRequestFactory::class);
        ClassDiscovery::setStrategies([]);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithMinConstraint(): void
    {
        $rule = new HaveIBeenPwned(5, null);

        // Force generation of code coverage
        $ruleConstruct = new HaveIBeenPwned(5, null);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithMaxConstraint(): void
    {
        $rule = new HaveIBeenPwned(0, 10);

        // Force generation of code coverage
        $ruleConstruct = new HaveIBeenPwned(0, 10);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithBothMinAndMaxConstraint(): void
    {
        $rule = new HaveIBeenPwned(5, 10);

        // Force generation of code coverage
        $ruleConstruct = new HaveIBeenPwned(5, 10);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithNegativeMinConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = new HaveIBeenPwned(-10);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithMaxConstraintSmallerThanMinConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = new HaveIBeenPwned(10, 5);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructUnconstrainedRule(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = new HaveIBeenPwned(0, null);
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
        $rule = new HaveIBeenPwned(5, 10);
        $ruleConstruct = new HaveIBeenPwned(5, 10);
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
        $rule = new HaveIBeenPwned(5, 10);
        $ruleConstruct = new HaveIBeenPwned(5, 10);
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
        $rule = new HaveIBeenPwned(5, 10);

        self::assertSame(5, $rule->getMin());
    }

    /**
     * @covers ::getMax
     */
    public function testCanGetMaxConstraint(): void
    {
        $rule = new HaveIBeenPwned(5, 10);

        self::assertSame(10, $rule->getMax());
    }

    /**
     * @covers ::test
     */
    public function testMinConstraintCanBeSatisfied(): void
    {
        $rule = new HaveIBeenPwned(5, null);

        self::assertTrue($rule->test('6004468405'));
    }

    /**
     * @covers ::test
     */
    public function testMinConstraintCanBeUnsatisfied(): void
    {
        $rule = new HaveIBeenPwned(5, null);

        self::assertFalse($rule->test('6597812222'));
    }

    /**
     * @covers ::test
     */
    public function testMinConstraintIsNotSatisfiedWhenHashIsNotFound(): void
    {
        $rule = new HaveIBeenPwned(1, null);

        self::assertFalse($rule->test('291vnnzrvtu9'));
    }

    /**
     * @covers ::test
     */
    public function testMaxConstraintCanBeSatisfied(): void
    {
        $rule = new HaveIBeenPwned(0, 3);

        self::assertTrue($rule->test('553193251'));
    }

    /**
     * @covers ::test
     */
    public function testMaxConstraintCanBeUnsatisfied(): void
    {
        $rule = new HaveIBeenPwned(0, 3);

        self::assertFalse($rule->test('+79250455754'));
    }

    /**
     * @covers ::test
     */
    public function testMaxConstraintIsSatisfiedWhenHashIsNotFound(): void
    {
        $rule = new HaveIBeenPwned(0, 5);

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

        $rule = new HaveIBeenPwned(0, 5);
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

        $rule = new HaveIBeenPwned(0, 5);

        $this->expectException(LogicException::class);

        $rule->test('291vnnzrvtu9');
    }

    /**
     * @covers ::test
     */
    public function testExceptionIsThrownWhenNoClientIsRegistered(): void
    {
        ClassDiscovery::setStrategies([]);

        $rule = new HaveIBeenPwned(0, 5);

        $this->expectException(LogicException::class);

        $rule->test('291vnnzrvtu9');
    }

    /**
     * @covers ::enforce
     */
    public function testEnforceDoesNotThrowExceptionWhenRuleIsSatisfied(): void
    {
        $client = new HaveIBeenPwnedClient();
        $requestFactory = new MockedRequestFactory();

        $rule = new HaveIBeenPwned(2, null);
        $rule->setClient($client);
        $rule->setRequestFactory($requestFactory);
        $translator = new Translator('en_US');

        $rule->enforce('1397wpfk', $translator);

        // Force generation of code coverage
        $ruleConstruct = new HaveIBeenPwned(2, null);
        $ruleConstruct->setClient($client);
        $ruleConstruct->setRequestFactory($requestFactory);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::enforce
     */
    public function testEnforceThrowsExceptionWhenRuleIsNotSatisfied(): void
    {
        $rule = new HaveIBeenPwned(3, null);
        $translator = new Translator('en_US');

        $this->expectException(RuleException::class);

        $rule->enforce('1397wpfk', $translator);
    }

    /**
     * @covers ::getMessage
     */
    public function testCanGetMessageForRuleWithMinConstraint(): void
    {
        $translator = new Translator('en_US');
        $rule = new HaveIBeenPwned(5, null);

        self::assertSame('Must appear at least 5 times in breaches.', $rule->getMessage($translator));
    }

    /**
     * @covers ::getMessage
     */
    public function testCanGetMessageForRuleWithMaxConstraint(): void
    {
        $translator = new Translator('en_US');
        $rule = new HaveIBeenPwned(0, 10);

        self::assertSame('Must appear at most 10 times in breaches.', $rule->getMessage($translator));
    }

    /**
     * @covers ::getMessage
     */
    public function testCanGetMessageForRuleWithBothMinAndMaxConstraint(): void
    {
        $translator = new Translator('en_US');
        $rule = new HaveIBeenPwned(5, 10);

        self::assertSame('Must appear between 5 and 10 times in breaches.', $rule->getMessage($translator));
    }

    /**
     * @covers ::getMessage
     */
    public function testCanGetMessageForRuleWithMaxConstraintEqualToZero(): void
    {
        $translator = new Translator('en_US');
        $rule = new HaveIBeenPwned(0, 0);

        self::assertSame('Must not appear in any breaches.', $rule->getMessage($translator));
    }

    /**
     * @covers ::getMessage
     */
    public function testCanGetMessageForRuleWithMinConstraintEqualToMaxConstraint(): void
    {
        $translator = new Translator('en_US');
        $rule = new HaveIBeenPwned(3, 3);

        self::assertSame('Must appear exactly 3 times in breaches.', $rule->getMessage($translator));
    }
}
