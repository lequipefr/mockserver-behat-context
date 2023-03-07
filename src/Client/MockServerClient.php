<?php

declare(strict_types=1);

namespace Lequipe\MockServer\Client;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\UriResolver;
use GuzzleHttp\Psr7\Utils;
use Http\Discovery\Psr18ClientDiscovery;
use Lequipe\MockServer\Builder\Expectation;
use Lequipe\MockServer\Builder\Verification;
use Lequipe\MockServer\Utils as LequipeUtils;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Mockserver client that uses a psr18 http client to request mockserver.
 */
class MockServerClient implements MockServerClientInterface
{
    /**
     * @param string $baseUri The base uri of your Mockserver instance. I.e "http://127.0.0.1:1080"
     * @param ?ClientInterface $client The Psr18 http client to use to query Mockserver.
     *                                 If not provided, will try to instanciate one from your vendors.
     */
    public function __construct(
        private string $baseUri,
        private ?ClientInterface $client = null,
    ) {
        if (null === $this->client) {
            $this->client = Psr18ClientDiscovery::find();
        }
    }

    private function sendJsonRequest(string $method, string $uri, array $parameters = null): ResponseInterface
    {
        $absoluteUri = UriResolver::resolve(new Uri($this->baseUri), new Uri($uri));
        $headers = [];
        $body = null;

        if (null !== $parameters) {
            $headers = ['content-type' => 'application/json'];
            $body = Utils::streamFor(json_encode($parameters));
        }

        $request = new Request($method, $absoluteUri, $headers, $body);

        return $this->client->sendRequest($request);
    }

    /**
     * @param array|Expectation $parameters
     */
    public function expectation($parameters): void
    {
        $this->sendJsonRequest('PUT', 'expectation', LequipeUtils::toArray($parameters, Expectation::class));
    }

    /**
     * @param array|Verification $parameters
     */
    public function verify($parameters): bool
    {
        $response = $this->sendJsonRequest('PUT', 'verify', LequipeUtils::toArray($parameters, Verification::class));

        return 202 === $response->getStatusCode();
    }

    public function reset(): void
    {
        $this->sendJsonRequest('PUT', 'reset');
    }
}
