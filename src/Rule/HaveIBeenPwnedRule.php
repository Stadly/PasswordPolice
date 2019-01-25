<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use Http\Factory\Discovery\HttpClient;
use Http\Factory\Discovery\HttpFactory;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use RuntimeException;
use StableSort\StableSort;
use Stadly\PasswordPolice\Constraint\CountConstraint;
use Stadly\PasswordPolice\Policy;
use Stadly\PasswordPolice\Rule;
use Stadly\PasswordPolice\ValidationError;

final class HaveIBeenPwnedRule implements Rule
{
    /**
     * @var CountConstraint[] Rule constraints.
     */
    private $constraints;

    /**
     * @var ClientInterface|null HTTP client for sending requests.
     */
    private $client;

    /**
     * @var RequestFactoryInterface|null Request factory for generating HTTP requests.
     */
    private $requestFactory;

    /**
     * @param int|null $max Maximum number of appearances in breaches.
     * @param int $min Minimum number of appearances in breaches.
     * @param int $weight Constraint weight.
     */
    public function __construct(?int $max = 0, int $min = 0, int $weight = 1)
    {
        $this->addConstraint($max, $min, $weight);
    }

    /**
     * @param int|null $max Maximum number of appearances in breaches.
     * @param int $min Minimum number of appearances in breaches.
     * @param int $weight Constraint weight.
     * @return $this
     */
    public function addConstraint(?int $max = 0, int $min = 0, int $weight = 1): self
    {
        $this->constraints[] = new CountConstraint($min, $max, $weight);

        StableSort::usort($this->constraints, static function (CountConstraint $a, CountConstraint $b): int {
            return $b->getWeight() <=> $a->getWeight();
        });

        return $this;
    }

    /**
     * @param ClientInterface $client HTTP client for sending requests.
     */
    public function setClient(ClientInterface $client): void
    {
        $this->client = $client;
    }

    /**
     * @return ClientInterface HTTP client for sending requests.
     * @throws RuntimeException If a client could not be found.
     */
    private function getClient(): ClientInterface
    {
        if ($this->client === null) {
            $client = HttpClient::client();
            $this->client = $client;
        }
        return $this->client;
    }

    /**
     * @param RequestFactoryInterface $requestFactory Request factory for generating HTTP requests.
     */
    public function setRequestFactory(RequestFactoryInterface $requestFactory): void
    {
        $this->requestFactory = $requestFactory;
    }

    /**
     * @return RequestFactoryInterface Request factory for generating HTTP requests.
     * @throws RuntimeException If a request factory could not be found.
     */
    private function getRequestFactory(): RequestFactoryInterface
    {
        if ($this->requestFactory === null) {
            $this->requestFactory = HttpFactory::requestFactory();
        }
        return $this->requestFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function test($password, ?int $weight = 1): bool
    {
        $count = $this->getCount((string)$password);
        $constraint = $this->getViolation($count, $weight);

        return $constraint === null;
    }

    /**
     * {@inheritDoc}
     */
    public function validate($password): ?ValidationError
    {
        $count = $this->getCount((string)$password);
        $constraint = $this->getViolation($count);

        if ($constraint !== null) {
            return new ValidationError(
                $this->getMessage($constraint, $count),
                $password,
                $this,
                $constraint->getWeight()
            );
        }

        return null;
    }

    /**
     * @param int $count Number of appearances in breaches.
     * @param int|null $weight Don't consider constraints with lower weights.
     * @return CountConstraint|null Constraint violated by the count.
     */
    private function getViolation(int $count, ?int $weight = null): ?CountConstraint
    {
        foreach ($this->constraints as $constraint) {
            if ($weight !== null && $constraint->getWeight() < $weight) {
                continue;
            }
            if (!$constraint->test($count)) {
                return $constraint;
            }
        }

        return null;
    }

    /**
     * @param string $password Password to check in breaches.
     * @return int Number of appearances in breaches.
     * @throws RuleException If an error occurred.
     */
    private function getCount(string $password): int
    {
        $sha1 = strtoupper(sha1($password));
        $prefix = substr($sha1, 0, 5);
        $suffix = substr($sha1, 5, 35);

        try {
            $requestFactory = $this->getRequestFactory();
            $request = $requestFactory->createRequest('GET', 'https://api.pwnedpasswords.com/range/'.$prefix);

            $client = $this->getClient();

            $response = $client->sendRequest($request);
            $body = $response->getBody();
            $contents = $body->getContents();
            $lines = explode("\r\n", $contents);
            foreach ($lines as $line) {
                if (substr($line, 0, 35) === $suffix) {
                    return (int)substr($line, 36);
                }
            }
            return 0;
        } catch (ClientExceptionInterface | RuntimeException $exception) {
            throw new RuleException(
                $this,
                'An error occurred while using the Have I Been Pwned? service: '.$exception->getMessage(),
                $exception
            );
        }
    }

    /**
     * @param CountConstraint $constraint Constraint that is violated.
     * @param int $count Count that violates the constraint.
     * @return string Message explaining the violation.
     */
    private function getMessage(CountConstraint $constraint, int $count): string
    {
        $translator = Policy::getTranslator();

        if ($constraint->getMax() === null) {
            return $translator->trans(
                'The password must appear at least once in data breaches.|'.
                'The password must appear at least %count% times in data breaches.',
                ['%count%' => $constraint->getMin()]
            );
        }

        if ($constraint->getMax() === 0) {
            return $translator->trans(
                'The password cannot appear in data breaches.'
            );
        }

        if ($constraint->getMin() === 0) {
            return $translator->trans(
                'The password must appear at most once in data breaches.|'.
                'The password must appear at most %count% times in data breaches.',
                ['%count%' => $constraint->getMax()]
            );
        }

        if ($constraint->getMin() === $constraint->getMax()) {
            return $translator->trans(
                'The password must appear exactly once in data breaches.|'.
                'The password must appear exactly %count% times in data breaches.',
                ['%count%' => $constraint->getMin()]
            );
        }

        return $translator->trans(
            'The password must appear between %min% and %max% times in data breaches.',
            ['%min%' => $constraint->getMin(), '%max%' => $constraint->getMax()]
        );
    }
}
