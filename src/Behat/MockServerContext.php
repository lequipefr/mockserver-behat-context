<?php

declare(strict_types=1);

namespace Lequipe\MockServer\Behat;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\PyStringNode;
use Lequipe\MockServer\Client\MockServerClient;
use Lequipe\MockServer\Client\MockServerClientInterface;
use Lequipe\MockServer\Exception\Exception;
use Lequipe\MockServer\Builder\Expectation;
use Lequipe\MockServer\Builder\HttpRequest;
use Lequipe\MockServer\Builder\Verification;
use TypeError;

class MockServerContext implements Context
{
    protected MockServerClientInterface $client;

    protected string $featurePath;

    private bool $shouldResetBefore;
    private bool $shouldResetAfter;

    private ?Expectation $currentExpectation = null;

    /**
     * Pass only url here to simplify configuration in behat.yml
     *
     * @param string|MockServerClientInterface $mockServer
     *      Either a url to mockserver, i.e:
     *
     *          - Lequipe\MockServer\Behat\MockServerContext:
     *              mockServer: "http://127.0.0.1:1080"
     *
     *      or an array with keys class and arguments, i.e:
     *
     *          - Lequipe\MockServer\Behat\MockServerContext:
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
            $this->client = new MockServerClient($mockServer);
        } elseif (is_array($mockServer) && isset($mockServer['class'])) {
            $arguments = $mockServer['arguments'] ?? [];
            $this->client = new $mockServer['class'](...$arguments);
        } elseif ($mockServer instanceof MockServerClientInterface) {
            $this->client = $mockServer;
        } else {
            throw new TypeError(
                'Expected $mockServer to be a string, array with keys "class" and "arguments", or an instance of ' . MockServerClientInterface::class,
            );
        }
    }

    public function getClient(): MockServerClientInterface
    {
        return $this->client;
    }

    protected function getCurrentExpectation(): Expectation
    {
        if (null === $this->currentExpectation) {
            $this->currentExpectation = new Expectation();
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
        $this->shouldResetBefore = true;
        $this->shouldResetAfter = false;
        $this->currentExpectation = null;
    }

    /**
     * @AfterScenario
     */
    public function afterScenario(): void
    {
        if (null !== $this->currentExpectation) {
            throw new Exception('An expectation is currently building and has not been sent');
        }

        if ($this->shouldResetAfter) {
            $this->client->reset();
            $this->shouldResetAfter = false;
        }
    }

    /**
     * /!\ if you create custom phrases that send expectation,
     *     use this method to make sure to clear previous expectation if needed.
     *
     * Should be called before any call to api,
     * to make sure to reset mocks from previous scenario
     * and reset expectations after scenario if necessary,
     * but only if scenario is using mockserver.
     */
    protected function resetMockserverBeforeFirstApiCall(): void
    {
        $this->shouldResetAfter = true;

        if ($this->shouldResetBefore) {
            $this->client->reset();
            $this->shouldResetBefore = false;
        }
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
        $this->resetMockserverBeforeFirstApiCall();

        $this->getCurrentExpectation()->httpRequest()
            ->method($method)
            ->pathWithParameters($path)
        ;

        $this->getCurrentExpectation()->httpResponse()
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

        $this->shouldResetBefore = false;
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
        $this->getCurrentExpectation()->httpRequest()->addHeader($name, $value);
    }

    /**
     * @Given the response status code will be :statusCode
     *
     * Example:
     *
     * Given the response status code will be 201
     * And the request "GET" "/users/1" will return the json:
     * """
     * [
     *   {
     *      "id": 1,
     *      "name": "Zidane edited"
     *   }
     * ]
     * """
     */
    public function iExpectRequestStatusCode(int $statusCode): void
    {
        $this->getCurrentExpectation()->httpResponse()->statusCode($statusCode);
    }

    /**
     * @Given I will receive this raw body:
     */
    public function iWillReceiveTheRawBody(PyStringNode $node): void
    {
        $this->getCurrentExpectation()->httpRequest()
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
        $this->getCurrentExpectation()->httpRequest()
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
        $this->resetMockserverBeforeFirstApiCall();

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
        $verification = new Verification();

        $verification
            ->exactly($times)
            ->httpRequest()
                ->method($method)
                ->pathWithParameters($path)
        ;

        $this->client->verify($verification->toArray());
    }
}
