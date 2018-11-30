<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use Http\Discovery\Exception\NotFoundException;
use Http\Discovery\HttpClientDiscovery;
use Http\Factory\Discovery\HttpFactory;
use InvalidArgumentException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use RuntimeException;
use Stadly\PasswordPolice\Policy;

final class HaveIBeenPwned implements RuleInterface
{
    /**
     * @var int Minimum number of appearances in breaches.
     */
    private $min;

    /**
     * @var int|null Maximum number of appearances in breaches.
     */
    private $max;

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
     */
    public function __construct(?int $max = 0, int $min = 0)
    {
        if ($min < 0) {
            throw new InvalidArgumentException('Min cannot be negative.');
        }
        if ($max !== null && $max < $min) {
            throw new InvalidArgumentException('Max cannot be smaller than min.');
        }
        if ($min === 0 && $max === null) {
            throw new InvalidArgumentException('Min cannot be zero when max is unconstrained.');
        }

        $this->min = $min;
        $this->max = $max;
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
        if (null === $this->client) {
            $this->client = HttpClientDiscovery::find();
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
        if (null === $this->requestFactory) {
            $this->requestFactory = HttpFactory::requestFactory();
        }
        return $this->requestFactory;
    }

    /**
     * @return int Minimum number of appearances in breaches.
     */
    public function getMin(): int
    {
        return $this->min;
    }

    /**
     * @return int|null Maximum number of appearances in breaches.
     */
    public function getMax(): ?int
    {
        return $this->max;
    }

    /**
     * {@inheritDoc}
     */
    public function test($password): bool
    {
        $count = $this->getNoncompliantCount((string)$password);

        return $count === null;
    }

    /**
     * {@inheritDoc}
     */
    public function enforce($password): void
    {
        $count = $this->getNoncompliantCount((string)$password);

        if ($count !== null) {
            throw new RuleException($this, $this->getMessage());
        }
    }

    /**
     * @param string $password Password to count appearances in breaches for.
     * @return int Number of appearances in breaches if not in compliance with the rule.
     * @throws TestException If an error occurred while using the Have I Been Pwned? service.
     */
    private function getNoncompliantCount(string $password): ?int
    {
        $count = $this->getCount($password);

        if ($count < $this->min) {
            return $count;
        }

        if (null !== $this->max && $this->max < $count) {
            return $count;
        }

        return null;
    }

    /**
     * @param string $password Password to check in breaches.
     * @return int Number of appearances in breaches.
     * @throws TestException If an error occurred while using the Have I Been Pwned? service.
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
            throw new TestException($this, 'An error occurred while using the Have I Been Pwned? service.', $exception);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getMessage(): string
    {
        $translator = Policy::getTranslator();

        if ($this->getMax() === null) {
            return $translator->trans(
                'Must appear at least once in breaches.|'.
                'Must appear at least %count% times in breaches.',
                ['%count%' => $this->getMin()]
            );
        }

        if ($this->getMax() === 0) {
            return $translator->trans(
                'Must not appear in any breaches.'
            );
        }

        if ($this->getMin() === 0) {
            return $translator->trans(
                'Must appear at most once in breaches.|'.
                'Must appear at most %count% times in breaches.',
                ['%count%' => $this->getMax()]
            );
        }

        if ($this->getMin() === $this->getMax()) {
            return $translator->trans(
                'Must appear exactly once in breaches.|'.
                'Must appear exactly %count% times in breaches.',
                ['%count%' => $this->getMin()]
            );
        }

        return $translator->trans(
            'Must appear between %min% and %max% times in breaches.',
            ['%min%' => $this->getMin(), '%max%' => $this->getMax()]
        );
    }
}
