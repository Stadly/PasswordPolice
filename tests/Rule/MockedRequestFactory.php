<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use PHPUnit\Framework\MockObject\Generator;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;

final class MockedRequestFactory implements RequestFactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createRequest(string $method, $uri): RequestInterface
    {
        $generator = new Generator();

        /**
         * @var MockObject&RequestInterface
         */
        $request = $generator->getMock(RequestInterface::class);

        return $request;
    }
}
