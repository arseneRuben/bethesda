<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GraphQLClient
{
    private $client;
    private $endpoint;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
        $this->endpoint = 'http://localhost:8084/graphql'; // URL de l'API GraphQL qui est en local pour l'instant
    }

    public function query(string $query, array $variables = []): array
    {
        $response = $this->client->request('POST', $this->endpoint, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'query' => $query,
                'variables' => $variables,
            ],
        ]);

        $statusCode = $response->getStatusCode();

        if ($statusCode !== 200) {
            throw new \RuntimeException("GraphQL API Error: {$response->getContent(false)}");
        }

        return $response->toArray();
    }
}
