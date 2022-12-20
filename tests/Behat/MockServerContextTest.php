<?php

declare(strict_types=1);

namespace Lequipe\Test\MockServer\Behat;

use Behat\Gherkin\Node\PyStringNode;
use Lequipe\MockServer\Exception\Exception;
use Lequipe\MockServer\Behat\MockServerContext;
use Lequipe\MockServer\Client\MockServerClient;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

class MockServerContextTest extends TestCase
{
    use ProphecyTrait;

    /**
     * Functions like "iWillReceiveTheHeader" add request elements to assert
     * from the next expectation, but the expectation is not send until
     * phrases like "theRequestOnApiWillReturnJson".
     *
     * Context throw an exception if at the end of a scenario,
     * an expectation is still being built, but has not been sent.
     */
    public function testAfterScenarioCheckNotSentExpectations()
    {
        $client = $this->prophesize(MockServerClient::class);

        $client->reset();
        $client->expectation(Argument::any());

        $context = new MockServerContext($client->reveal());

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('An expectation is currently building and has not been sent');

        $context->beforeScenario();
        $context->iWillReceiveTheHeader('Header', 'value');
        $context->afterScenario();
    }
}
