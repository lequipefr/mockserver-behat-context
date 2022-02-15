<?php

declare(strict_types=1);

namespace Lequipe\MockServer;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\PyStringNode;
use Symfony\Component\HttpClient\HttpClient;

class MockServerContext implements Context
{
    private MockServerClient $client;

    private string $featurePath;

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
     * Store feature file path in order to load json file.
     *
     * @BeforeScenario
     */
    public function storeFeatureFile(BeforeScenarioScope $scope): void
    {
        $this->featurePath = dirname($scope->getFeature()->getFile());
    }

    private function theRequestOnApiWillReturnBody(string $method, string $path, string $body): void
    {
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
