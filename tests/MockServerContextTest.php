<?php

declare(strict_types=1);

namespace Lequipe\Test\MockServer;

use Behat\Gherkin\Node\PyStringNode;
use Lequipe\MockServer\Exception\Exception;
use Lequipe\MockServer\MockServerClient;
use Lequipe\MockServer\MockServerContext;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Contracts\HttpClient\ResponseInterface;

class MockServerContextTest extends TestCase
{
    use ProphecyTrait;

    public function testResetIsCalledOnceOnApiCall()
    {
        $client = $this->prophesize(MockServerClient::class);

        $client
            ->reset()
            ->shouldBeCalledTimes(1)
        ;

        $client
            ->expectation(Argument::any())
        ;

        $context = new MockServerContext($client->reveal());

        $context->beforeScenario();
        $context->theRequestOnApiWillReturnJson('get', '/users', new PyStringNode(['[]'], 0));
        $context->theRequestOnApiWillReturnJson('get', '/users/1', new PyStringNode(['{}'], 0));
    }

    public function testResetIsNotCalledIfNoApiCall()
    {
        $client = $this->prophesize(MockServerClient::class);

        $client
            ->reset()
            ->shouldBeCalledTimes(0)
        ;

        $context = new MockServerContext($client->reveal());

        $context->beforeScenario();
        $context->afterScenario();
    }

    public function testResetIsNotCalledOnTheEndOfScenatioIfNotNecessary()
    {
        $client = $this->prophesize(MockServerClient::class);

        $client
            ->reset()
            ->shouldBeCalledTimes(0)
        ;

        $client
            ->expectation(Argument::any())
        ;

        $context = new MockServerContext($client->reveal());

        $context->beforeScenario();
        $context->afterScenario();
    }

    public function testResetIsCalledOnTheEndOfScenatioIfNecessary()
    {
        $client = $this->prophesize(MockServerClient::class);

        $client
            ->reset()
            ->shouldBeCalledTimes(2)
        ;

        $client
            ->expectation(Argument::any())
        ;

        $context = new MockServerContext($client->reveal());

        $context->beforeScenario();
        $context->theRequestOnApiWillReturnJson('get', '/users', new PyStringNode(['[]'], 0));
        $context->theRequestOnApiWillReturnJson('get', '/users/1', new PyStringNode(['{}'], 0));
        $context->afterScenario();
    }

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

        $client
            ->expectation(Argument::any())
        ;

        $context = new MockServerContext($client->reveal());

        $context->beforeScenario();
        $context->iWillReceiveTheHeader('Header', 'value');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('An expectation is currently building and has not been sent');

        $context->afterScenario();
    }
}
