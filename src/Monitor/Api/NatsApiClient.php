<?php

declare(strict_types=1);

namespace EJTJ3\PhpNats\Monitor\Api;

use EJTJ3\PhpNats\Connection\NatsConnectionOption;
use EJTJ3\PhpNats\Connection\Server;
use EJTJ3\PhpNats\Connection\ServerCollection;
use EJTJ3\PhpNats\Monitor\Model\ConnectionRequest;
use EJTJ3\PhpNats\Monitor\Model\ConnectionResponse;
use EJTJ3\PhpNats\Monitor\RequestBuilder;
use Http\Discovery\NotFoundException;
use Http\Discovery\Psr18ClientDiscovery;
use JMS\Serializer\SerializerInterface;
use LogicException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;

final class NatsApiClient
{
    private const ROUTE_CONNZ = 'connz';

    private RequestBuilder $requestBuilder;

    private ClientInterface $client;

    private SerializerInterface $serializer;

    public function __construct(
        SerializerInterface $serializer,
        ClientInterface     $client = null,
        RequestBuilder      $requestBuilder = null
    )
    {
        if ($client === null) {
            try {
                $client = Psr18ClientDiscovery::find();
            } catch (NotFoundException $e) {
                throw new LogicException('Could not find any installed HTTP clients. Try installing a package for this list: https://packagist.org/providers/psr/http-client-implementation', 0, $e);
            }
        }

        $this->requestBuilder = $requestBuilder ?: new RequestBuilder();
        $this->client = $client;
        $this->serializer = $serializer;
    }

    /**
     *
     *
     * @return ConnectionResponse[]
     *
     * @throws ClientExceptionInterface
     *
     */
    public function getConnections(ConnectionRequest $requestOption, NatsConnectionOption $option): array
    {
        return $this->doRequests($option->getServerCollection(), ConnectionResponse::class, self::ROUTE_CONNZ, $requestOption->toArray());
    }

    /**
     * @return ResponseInterface[]
     * @throws ClientExceptionInterface
     */
    private function doRequests(ServerCollection $serverCollection, string $type, string $route, array $queryParams = []): array
    {
        $responses = [];
        foreach ($serverCollection->getServers() as $server) {
            // Create Request
            $request = $this->requestBuilder->create(
                'GET',
                $this->generateUri($server, $route),
                [],
                $queryParams
            );

            // Send request async
            $responses[] = $this->client->sendRequest($request);
        }

        $models = [];
        foreach ($responses as $response) {
            $models[] = $this->serializer->deserialize($response->getBody()->getContents(), $type, 'json');
        }

        return $models;

    }

    private function generateUri(Server $server, string $route): string
    {
        return sprintf('%s://%s:%d/%s', $server->isTls() ? 'https' : 'http', $server->getHost(), $server->getPort(), $route);
    }
}
