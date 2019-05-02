<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use Http\Factory\Discovery\ClientLocator;
use Http\Factory\Discovery\FactoryLocator;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\Stub\Exception as StubException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use RuntimeException;
use Stadly\PasswordPolice\ValidationError;
use Symfony\Component\Translation\Translator;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Rule\HaveIBeenPwnedRule
 * @covers ::<private>
 * @covers ::<protected>
 * @covers ::__construct
 */
final class HaveIBeenPwnedRuleTest extends TestCase
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
     * @doesNotPerformAssertions
     */
    public function testCanConstructRuleWithMinConstraint(): void
    {
        $rule = new HaveIBeenPwnedRule(null, 5);
    }

    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructRuleWithMaxConstraint(): void
    {
        $rule = new HaveIBeenPwnedRule(10, 0);
    }

    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructRuleWithBothMinAndMaxConstraint(): void
    {
        $rule = new HaveIBeenPwnedRule(10, 5);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithNegativeMinConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = new HaveIBeenPwnedRule(null, -10);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithMaxConstraintSmallerThanMinConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = new HaveIBeenPwnedRule(5, 10);
    }

    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructUnconstrainedRule(): void
    {
        $rule = new HaveIBeenPwnedRule(null, 0);
    }

    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructRuleWithMinConstraintEqualToMaxConstraint(): void
    {
        $rule = new HaveIBeenPwnedRule(5, 5);
    }

    /**
     * @covers ::addConstraint
     */
    public function testCanAddConstraint(): void
    {
        $rule1 = new HaveIBeenPwnedRule(5, 5, 1);
        $rule1->addConstraint(10, 10, 2);

        $rule2 = new HaveIBeenPwnedRule(10, 10, 2);
        $rule2->addConstraint(5, 5, 1);
        self::assertEquals($rule1, $rule2);
    }

    /**
     * @covers ::setClient
     */
    public function testCanSetClient(): void
    {
        $rule1 = new HaveIBeenPwnedRule(10, 5);
        $rule2 = new HaveIBeenPwnedRule(10, 5);
        self::assertEquals($rule1, $rule2);

        $client = $this->createMock(ClientInterface::class);
        $rule1->setClient($client);
        self::assertNotEquals($rule1, $rule2);

        $rule2->setClient($client);
        self::assertEquals($rule1, $rule2);
    }

    /**
     * @covers ::setRequestFactory
     */
    public function testCanSetRequestFactory(): void
    {
        $rule1 = new HaveIBeenPwnedRule(10, 5);
        $rule2 = new HaveIBeenPwnedRule(10, 5);
        self::assertEquals($rule1, $rule2);

        $requestFactory = $this->createMock(RequestFactoryInterface::class);
        $rule1->setRequestFactory($requestFactory);
        self::assertNotEquals($rule1, $rule2);

        $rule2->setRequestFactory($requestFactory);
        self::assertEquals($rule1, $rule2);
    }

    /**
     * @covers ::test
     */
    public function testMinConstraintCanBeSatisfied(): void
    {
        $rule = new HaveIBeenPwnedRule(null, 5);

        self::assertTrue($rule->test('6004468405'));
    }

    /**
     * @covers ::test
     */
    public function testMinConstraintCanBeUnsatisfied(): void
    {
        $rule = new HaveIBeenPwnedRule(null, 5);

        self::assertFalse($rule->test('6597812222'));
    }

    /**
     * @covers ::test
     */
    public function testMinConstraintIsNotSatisfiedWhenHashIsNotFound(): void
    {
        $rule = new HaveIBeenPwnedRule(null, 1);

        self::assertFalse($rule->test('291vnnzrvtu9'));
    }

    /**
     * @covers ::test
     */
    public function testMaxConstraintCanBeSatisfied(): void
    {
        $rule = new HaveIBeenPwnedRule(3, 0);

        self::assertTrue($rule->test('553193251'));
    }

    /**
     * @covers ::test
     */
    public function testMaxConstraintCanBeUnsatisfied(): void
    {
        $rule = new HaveIBeenPwnedRule(3, 0);

        self::assertFalse($rule->test('+79250455754'));
    }

    /**
     * @covers ::test
     */
    public function testMaxConstraintIsSatisfiedWhenHashIsNotFound(): void
    {
        $rule = new HaveIBeenPwnedRule(5, 0);

        self::assertTrue($rule->test('291vnnzrvtu9'));
    }

    /**
     * @covers ::test
     */
    public function testRuleIsSatisfiedWhenConstraintWeightIsLowerThanTestWeight(): void
    {
        $rule = new HaveIBeenPwnedRule(3, 0, 1);

        self::assertTrue($rule->test('+79250455754', 2));
    }

    /**
     * @covers ::test
     */
    public function testErrorsWhenCalculatingCountAreHandled(): void
    {
        $exception = $this->createMock(ClientExceptionInterface::class);
        $stubException = new StubException($exception);

        $client = $this->createMock(ClientInterface::class);
        $client->method('sendRequest')->will($stubException);

        $rule = new HaveIBeenPwnedRule(5, 0);
        $rule->setClient($client);

        $this->expectException(RuleException::class);

        $rule->test('291vnnzrvtu9');
    }

    /**
     * @covers ::test
     */
    public function testExceptionIsThrownWhenNoRequestFactoryIsRegistered(): void
    {
        FactoryLocator::unregister(RequestFactoryInterface::class, MockedRequestFactory::class);

        $rule = new HaveIBeenPwnedRule(5, 0);

        $this->expectException(RuntimeException::class);

        $rule->test('291vnnzrvtu9');
    }

    /**
     * @covers ::test
     */
    public function testExceptionIsThrownWhenNoClientIsRegistered(): void
    {
        ClientLocator::unregister(ClientInterface::class, HaveIBeenPwnedClient::class);

        $rule = new HaveIBeenPwnedRule(5, 0);

        $this->expectException(RuntimeException::class);

        $rule->test('291vnnzrvtu9');
    }

    /**
     * @covers ::validate
     */
    public function testRuleCanBeValidated(): void
    {
        $rule = new HaveIBeenPwnedRule(null, 2);

        self::assertNull($rule->validate('1397wpfk', new Translator('en_US')));
    }

    /**
     * @covers ::validate
     */
    public function testRuleWithMinConstraintCanBeInvalidated(): void
    {
        $rule = new HaveIBeenPwnedRule(null, 5);

        self::assertEquals(
            new ValidationError(
                'The password must appear at least 5 times in data breaches.',
                '1397wpfk',
                $rule,
                1
            ),
            $rule->validate('1397wpfk', new Translator('en_US'))
        );
    }

    /**
     * @covers ::validate
     */
    public function testRuleWithMaxConstraintCanBeInvalidated(): void
    {
        $rule = new HaveIBeenPwnedRule(10, 0);

        self::assertEquals(
            new ValidationError(
                'The password must appear at most 10 times in data breaches.',
                '6004468405',
                $rule,
                1
            ),
            $rule->validate('6004468405', new Translator('en_US'))
        );
    }

    /**
     * @covers ::validate
     */
    public function testRuleWithBothMinAndMaxConstraintCanBeInvalidated(): void
    {
        $rule = new HaveIBeenPwnedRule(10, 5);

        self::assertEquals(
            new ValidationError(
                'The password must appear between 5 and 10 times in data breaches.',
                '1397wpfk',
                $rule,
                1
            ),
            $rule->validate('1397wpfk', new Translator('en_US'))
        );
    }

    /**
     * @covers ::validate
     */
    public function testRuleWithMaxConstraintEqualToZeroCanBeInvalidated(): void
    {
        $rule = new HaveIBeenPwnedRule(0, 0);

        self::assertEquals(
            new ValidationError(
                'The password cannot appear in data breaches.',
                '1397wpfk',
                $rule,
                1
            ),
            $rule->validate('1397wpfk', new Translator('en_US'))
        );
    }

    /**
     * @covers ::validate
     */
    public function testRuleWithMinConstraintEqualToMaxConstraintCanBeInvalidated(): void
    {
        $rule = new HaveIBeenPwnedRule(3, 3);

        self::assertEquals(
            new ValidationError(
                'The password must appear exactly 3 times in data breaches.',
                '1397wpfk',
                $rule,
                1
            ),
            $rule->validate('1397wpfk', new Translator('en_US'))
        );
    }
}
