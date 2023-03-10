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
     * @Then mockserver should have been reset
     */
    public function mockserverShouldHaveBeenResetted(): void
    {
        assertEquals(
            1,
            $this->client->getResetCallsCount()
                - 1 // do not count the initial reset, called before the expectation
            ,
            'Mockserver should have been reset',
        );
    }

    /**
     * @Then print expectations
     */
    public function printExpectations(): void
    {
        echo json_encode($this->client->getExpectations(), JSON_PRETTY_PRINT);
    }
}
