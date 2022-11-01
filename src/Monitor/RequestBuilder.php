<?php

declare(strict_types=1);

namespace EJTJ3\PhpNats\Monitor;

use FriendsOfPHP\WellKnownImplementations\WellKnownPsr17Factory;
use Http\Discovery\Psr17FactoryDiscovery;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Http\Discovery\Exception\NotFoundException;
use LogicException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;

/**
 * @author Evert Jan Hakvoort <evertjan@hakvoort.io>
 *
 * @internal
 */
final class RequestBuilder
{
    private RequestFactoryInterface $requestFactory;

    private StreamFactoryInterface $streamFactory;

    public function __construct(
        RequestFactoryInterface $requestFactory = null,
        StreamFactoryInterface  $streamFactory = null
    )
    {
        $psr17Factory = new WellKnownPsr17Factory();

        $this->streamFactory = $requestFactory ?? $psr17Factory;
        $this->requestFactory = $streamFactory ?? $psr17Factory;
    }

    /**
     * Creates a new PSR-7 request.
     *
     * @param array $headers name => value or name=>[value]
     * @param StreamInterface|string|null $body request body
     */
    public function create(
        string $method,
        string $uri,
        array  $headers = [],
        array  $query = [],
               $body = null
    ): RequestInterface
    {
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
