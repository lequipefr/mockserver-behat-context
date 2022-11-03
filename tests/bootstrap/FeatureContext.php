<?php

declare(strict_types=1);

namespace Lequipe\Test\MockServer;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\Environment\InitializedContextEnvironment;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\PyStringNode;
use DomainException;
use Lequipe\MockServer\Behat\MockServerContext;

use function PHPUnit\Framework\assertEquals;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    private TraceableMockServerClient $client;

    /**
     * @BeforeScenario
     *
     * Retrieve traceable mockserver client from tested context.
     */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();

        if (!$environment instanceof InitializedContextEnvironment) {
            throw new DomainException('Expected an instance of InitializedContextEnvironment');
        }

        $context = $environment->getContext(MockServerContext::class);

        if (!$context instanceof MockServerContext) {
            throw new DomainException('Expected an instance of ' . MockServerContext::class);
        }

        $client = $context->getClient();

        if (!$client instanceof TraceableMockServerClient) {
            throw new DomainException('Expected an instance of TraceableMockServerClient');
        }

        $this->client = $client;
    }

    /**
     * @Then mockserver should receive the following expectation only:
     */
    public function mockserverShouldReceiveTheFollowingExpectationOnly(PyStringNode $string): void
    {
        $expected = json_decode($string->__toString(), true);
        $actuals = $this->client->getExpectations();

        assertEquals(1, count($actuals), 'Mockserver received exactly 1 expectation');
        assertEquals($expected, $actuals[0], 'Mockserver has received the expected expectation');
    }

    /**
     * @Then print expectations
     */
    public function printExpectations(): void
    {
        echo json_encode($this->client->getExpectations(), JSON_PRETTY_PRINT);
    }
}
