<?php

declare(strict_types=1);

namespace Lequipe\MockServer;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class MockServerClient implements MockServerClientInterface
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function expectation(array $parameters): void
    {
        $this->client->request('PUT', '/expectation', [
            'json' => $parameters,
        ]);
    }

    public function verify(array $parameters): void
    {
        $this->client->request('PUT', '/verify', [
            'json' => $parameters,
        ]);
    }

    public function reset(): void
    {
        $this->client->request('PUT', '/reset');
    }
}
