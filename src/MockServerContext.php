<?php

declare(strict_types=1);

namespace Lequipe\MockServer;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Symfony\Component\HttpClient\HttpClient;

class MockServerContext implements Context
{
    private MockServerClient $client;

    /**
     * @param string $mockServerUrl Url to mockserver, i.e "http://127.0.0.1:1080"
     */
    public function __construct(string $mockServerUrl)
    {
        $this->client = new MockServerClient(HttpClient::createForBaseUri($mockServerUrl));
    }

    /**
     * Clear previous expectations before scenario.
     *
     * @BeforeScenario @mockserver
     */
    public function clearMocks(): void
    {
        $this->client->reset();
    }

    /**
     * @Given the request :method :path will return the json:
     *
     * Example:
     *
     * Given the request "GET" "/users" will return the json:
     * """
     * [
     *   {
     *      "id": 1,
     *      "name": "Zidane"
     *   },
     *   {
     *      "id": 2,
     *      "name": "Barthez"
     *   }
     * ]
     * """
     */
    public function theRequestOnApiWillReturn(string $method, string $path, PyStringNode $node)
    {
        $this->client->expectation([
            'json' => [
                'httpRequest' => [
                    'method' => $method,
                    'path' => $path,
                ],
                'httpResponse' => [
                    'body' => json_decode($node->getRaw()),
                ],
            ],
        ]);
    }
}
