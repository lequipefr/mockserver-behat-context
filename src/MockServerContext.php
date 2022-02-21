<?php

declare(strict_types=1);

namespace Lequipe\MockServer;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\PyStringNode;
use InvalidArgumentException;
use Symfony\Component\HttpClient\HttpClient;

class MockServerContext implements Context
{
    private MockServerClient $client;

    private string $featurePath;

    private bool $shouldClearMocks;

    /**
     * Pass only url here to simplify configuration in behat.yml
     *
     * @param string|MockServerClient $mockServer Url to mockserver, i.e "http://127.0.0.1:1080",
     *                                            or an instance of MockServerClient
     */
    public function __construct($mockServer)
    {
        if (is_string($mockServer)) {
            $this->client = new MockServerClient(HttpClient::createForBaseUri($mockServer));
        } elseif ($mockServer instanceof MockServerClient) {
            $this->client = $mockServer;
        } else {
            throw new InvalidArgumentException(
                'Expected $mockServer to be a string or an instance of '.MockServerClient::class
            );
        }
    }

    /**
     * @BeforeScenario
     */
    public function beforeScenario(): void
    {
        $this->shouldClearMocks = true;
    }

    /**
     * Should be called before any call to api,
     * to make sure to reset mocks from previous scenario,
     * but only if scenario is using mockserver.
     */
    private function resetMockServerBeforeFirstApiCall(): void
    {
        if (!$this->shouldClearMocks) {
            return;
        }

        $this->client->reset();

        $this->shouldClearMocks = false;
    }

    /**
     * Store feature file path in order to load json file.
     *
     * @BeforeScenario
     */
    public function storeFeatureFile(BeforeScenarioScope $scope): void
    {
        $this->featurePath = dirname($scope->getFeature()->getFile());
    }

    private function theRequestOnApiWillReturnBody(string $method, string $path, $body): void
    {
        $this->resetMockServerBeforeFirstApiCall();

        $this->client->expectation([
            'json' => [
                'httpRequest' => [
                    'method' => $method,
                    'path' => $path,
                ],
                'httpResponse' => [
                    'body' => $body,
                ],
            ],
        ]);
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
    public function theRequestOnApiWillReturn(string $method, string $path, PyStringNode $node): void
    {
        $this->theRequestOnApiWillReturnBody($method, $path, json_decode($node->getRaw()));
    }

    /**
     * @Given the request :method :path will return the json from file :filename
     *
     * Example:
     *
     * Given the request "GET" "/users" will return the json from file "users/get-users.json"
     */
    public function theRequestOnApiWillReturnFromFile(string $method, string $path, string $filename): void
    {
        $json = file_get_contents($this->featurePath . DIRECTORY_SEPARATOR . $filename);

        $this->theRequestOnApiWillReturnBody($method, $path, $json);
    }
}
