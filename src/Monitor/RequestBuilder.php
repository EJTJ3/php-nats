<?php

declare(strict_types=1);

namespace EJTJ3\PhpNats\Monitor;

use FriendsOfPHP\WellKnownImplementations\WellKnownPsr17Factory;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;

/**
 * @author Evert Jan Hakvoort <evertjan@hakvoort.io>
 *
 * @internal
 */
final class RequestBuilder
{
    public function __construct(
        private readonly RequestFactoryInterface $requestFactory = new WellKnownPsr17Factory(),
        private readonly StreamFactoryInterface  $streamFactory = new WellKnownPsr17Factory()
    )
    {
    }

    /**
     * Creates a new PSR-7 request.
     *
     * @param array $headers name => value or name=>[value]
     */
    public function create(
        string $method,
        string $uri,
        array  $headers = [],
        array  $query = [],
        StreamInterface|string $body = null
    ): RequestInterface {
        if (count($query) !== 0) {
            $uri .= '?' . http_build_query($query);
        }

        $request = $this->requestFactory->createRequest($method, $uri);

        foreach ($headers as $name => $value) {
            $request = $request->withHeader($name, $value);
        }

        if ($body !== null) {
            if (!$body instanceof StreamInterface) {
                $body = $this->streamFactory->createStream($body);
            }

            $request = $request->withBody($body);
        }

        return $request;
    }
}
