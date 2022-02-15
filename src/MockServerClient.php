<?php

declare(strict_types=1);

namespace Lequipe\MockServer;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class MockServerClient
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function expectation(array $parameters): ResponseInterface
    {
        return $this->client->request('PUT', '/expectation', $parameters);
    }

    public function reset(): ResponseInterface
    {
        return $this->client->request('PUT', '/reset');
    }
}
