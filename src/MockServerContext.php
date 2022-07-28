<?php

declare(strict_types=1);

namespace Lequipe\MockServer;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\PyStringNode;
use Exception;
use InvalidArgumentException;
use Lequipe\MockServer\Expectation\ExpectationBuilder;
use Symfony\Component\HttpClient\HttpClient;

class MockServerContext implements Context
{
    protected MockServerClientInterface $client;

    protected string $featurePath;

    private bool $shouldClearMocks;

    private ?ExpectationBuilder $currentExpectation = null;

    /**
     * Pass only url here to simplify configuration in behat.yml
     *
     * @param string|MockServerClientInterface $mockServer
     *      Either a url to mockserver, i.e:
     *
     *          - Lequipe\MockServer\MockServerContext:
     *              mockServer: "http://127.0.0.1:1080"
     *
     *      or an array with keys class and arguments, i.e:
     *
     *          - Lequipe\MockServer\MockServerContext:
     *              mockServer:
     *                  class: App\MyCustomClient
     *                  arguments:
     *                      - 'argument'
     *
     *      or an instance of MockServerClientInterface (programmatic use).
     */
    public function __construct($mockServer)
    {
        if (is_string($mockServer)) {
            $this->client = new MockServerClient(HttpClient::createForBaseUri($mockServer));
        } elseif (is_array($mockServer) && isset($mockServer['class'])) {
            $arguments = $mockServer['arguments'] ?? [];
            $this->client = new $mockServer['class'](...$arguments);
        } elseif ($mockServer instanceof MockServerClientInterface) {
            $this->client = $mockServer;
        } else {
            throw new InvalidArgumentException(
                'Expected $mockServer to be a string, array with class/arguments, or an instance of '.MockServerClientInterface::class
            );
        }
    }

    public function getClient(): MockServerClientInterface
    {
        return $this->client;
    }

    protected function getCurrentExpectation(): ExpectationBuilder
    {
        if (null === $this->currentExpectation) {
            $this->currentExpectation = new ExpectationBuilder();
        }

        return $this->currentExpectation;
    }

    protected function resetCurrentExpectation(): void
    {
        $this->currentExpectation = null;
    }

    protected function dumpCurrentExpectation(): array
    {
        $params = $this->getCurrentExpectation()->toArray();

        $this->currentExpectation = null;

        return $params;
    }

    /**
     * @BeforeScenario
     */
    public function beforeScenario(): void
    {
        $this->shouldClearMocks = true;
        $this->currentExpectation = null;
    }

    /**
     * Should be called before any call to api,
     * to make sure to reset mocks from previous scenario,
     * but only if scenario is using mockserver.
     */
    protected function resetMockServerBeforeFirstApiCall(): void
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

    protected function theRequestOnApiWillReturnBody(string $method, string $path, array $body): void
    {
        $this->resetMockServerBeforeFirstApiCall();

        $parsedUrl = parse_url($path);

        $this->getCurrentExpectation()->expectedRequest()
            ->method($method)
            ->path($parsedUrl['path'])
        ;

        if (array_key_exists('query', $parsedUrl)) {
            $this->getCurrentExpectation()->expectedRequest()
                ->addQueryStringParametersFromString($parsedUrl['query'])
            ;
        }

        $this->getCurrentExpectation()->mockedResponse()
            ->bodyJson($body)
        ;

        $this->client->expectation($this->dumpCurrentExpectation());
    }

    /**
     * Manually clear mocks.
     *
     * @Given I reset mocks
     */
    public function iResetMocks(): void
    {
        $this->client->reset();

        $this->shouldClearMocks = false;
    }

    /**
     * Set an expected header in request in order to send the mock.
     *
     * @Given I will receive the header :name :value
     *
     * Example:
     *
     * Given I will receive the header "Content-Type" "application/json"
     * And the request "PATCH" "/users/1" will return the json:
     * """
     * [
     *   {
     *      "id": 1,
     *      "name": "Zidane edited"
     *   }
     * ]
     * """
     */
    public function iWillReceiveTheHeader(string $name, string $value): void
    {
        $this->getCurrentExpectation()->expectedRequest()->addHeader($name, $value);
    }

    /**
     * @Given I will receive this raw body:
     */
    public function iWillReceiveTheRawBody(PyStringNode $node): void
    {
        $this->getCurrentExpectation()->expectedRequest()
            ->bodyRaw($node->getRaw())
        ;
    }

    /**
     * Set an expected json payload that should match in order to send the mock.
     *
     * @Given I will receive this json payload:
     *
     * Example:
     *
     * Given I will receive this json payload:
     * """
     * {"name": "Zidane edited"}
     * """
     * And the request "PATCH" "/users/1" will return the json:
     * """
     * [
     *   {
     *      "id": 1,
     *      "name": "Zidane edited"
     *   }
     * ]
     * """
     */
    public function iExpectTheRequestJson(PyStringNode $node): void
    {
        $this->getCurrentExpectation()->expectedRequest()
            ->bodyJson(json_decode($node->getRaw(), true))
        ;
    }

    /**
     * Send custom expectation.
     *
     * @see https://app.swaggerhub.com/apis/jamesdbloom/mock-server-openapi/5.13.x#/expectation/put_expectation
     *
     * @Given I expect this request:
     *
     * Example:
     *
     *  Given I expect this request:
     *  """
     *  {
     *      "httpRequest": {
     *          "method": "get",
     *          "path": "/my/custom/path",
     *          "queryStringParameters": [
     *              {"name": "myParam", "values": ["possibleValue", "otherPossibleValue"]}
     *          ],
     *          "body": {
     *              "expected_body": "ok"
     *          }
     *      },
     *      "httpResponse": {
     *          "statusCode": 200,
     *          "body": {
     *              "my_custom_body": "ok"
     *          }
     *      }
     *  }
     *  """
     */
    public function iExpectThisRequest(PyStringNode $node): void
    {
        $this->resetMockServerBeforeFirstApiCall();

        $expectation = json_decode($node->getRaw(), true);

        $this->client->expectation($expectation);
    }

    /**
     * @Given the request :method :path will return body from file :filename
     *
     * Example:
     *
     * Given the request "GET" "index.html" will return body from file "mock-index.html"
     */
    public function theRequestWillReturnFromFile(string $method, string $path, string $filename): void
    {
        $content = file_get_contents($this->featurePath . DIRECTORY_SEPARATOR . $filename);

        $this->theRequestOnApiWillReturnBody($method, $path, ['string' => $content]);
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
    public function theRequestOnApiWillReturnJson(string $method, string $path, PyStringNode $node): void
    {
        $json = json_decode($node->getRaw(), true);

        if (null === $json) {
            throw new Exception('Error while parsing json.');
        }

        $this->theRequestOnApiWillReturnBody($method, $path, json_decode($node->getRaw(), true));
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
        $fullFilename = $this->featurePath . DIRECTORY_SEPARATOR . $filename;

        if (!file_exists($fullFilename)) {
            throw new Exception('File "' . $fullFilename . '" not found.');
        }

        $content = file_get_contents($fullFilename);
        $json = json_decode($content, true);

        if (null === $json) {
            throw new Exception('Error while parsing json from file "' . $fullFilename . '".');
        }

        $this->theRequestOnApiWillReturnBody($method, $path, json_decode($content, true));
    }

    /**
     * @Then the request :method :path should have been called exactly :times times
     *
     * Example:
     *
     *  When I send a "PUT" request on "/users/1"
     *  Then the request "PUT" "/sso/users/1" should have been called exactly 1 times
     */
    public function iVerify(string $method, string $path, int $times): void
    {
        $this->client->verify([
            'httpRequest' => [
                'method' => $method,
                'path' => $path,
            ],
            'times' => [
                'atLeast' => $times,
                'atMost' => $times,
            ],
        ]);
    }
}
