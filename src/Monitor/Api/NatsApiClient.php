<?php

declare(strict_types=1);

namespace EJTJ3\PhpNats\Monitor\Api;

use EJTJ3\PhpNats\Connection\NatsConnectionOption;
use EJTJ3\PhpNats\Connection\Server;
use EJTJ3\PhpNats\Connection\ServerCollection;
use EJTJ3\PhpNats\Monitor\Model\ConnectionRequest;
use EJTJ3\PhpNats\Monitor\Model\ConnectionResponse;
use EJTJ3\PhpNats\Monitor\RequestBuilder;
use FriendsOfPHP\WellKnownImplementations\WellKnownPsr18Client;
use JMS\Serializer\SerializerInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;

final class NatsApiClient
{
    private const ROUTE_CONNZ = 'connz';

    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ClientInterface     $client = new WellKnownPsr18Client(),
        private readonly RequestBuilder      $requestBuilder = new RequestBuilder()
    )
    {
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
