<?php

declare(strict_types=1);

use Behat\Behat\Context\Context;
use Behat\Behat\Context\Environment\InitializedContextEnvironment;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\PyStringNode;
use Lequipe\MockServer\MockServerContext;

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

        $context = $environment->getContext('Lequipe\MockServer\MockServerContext');

        if (!$context instanceof MockServerContext) {
            throw new DomainException('Expected an instance of MockServerContext');
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

        if (null === $expected) {
            throw new DomainException('Error while parsing json');
        }

        assertEquals(1, count($actuals), 'Mockserver received exactly 1 expectation');
        assertEquals($expected, $actuals[0], 'Mockserver has received the expected expectation');
    }

    /**
     * @Then mockserver should receive the following verification only:
     */
    public function mockserverShouldReceiveTheFollowingVerificationOnly(PyStringNode $string): void
    {
        $expected = json_decode($string->__toString(), true);
        $actuals = $this->client->getVerifications();

        assertEquals(1, count($actuals), 'Mockserver received exactly 1 verification');
        assertEquals($expected, $actuals[0], 'Mockserver has received the expected verification');
    }

    /**
     * @Then print expectations
     */
    public function printExpectations(): void
    {
        echo json_encode($this->client->getExpectations(), JSON_PRETTY_PRINT);
    }
}
