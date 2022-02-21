<?php

use Behat\Gherkin\Node\PyStringNode;
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
            ->willReturn($this->prophesize(ResponseInterface::class))
        ;

        $client
            ->expectation(Argument::any())
            ->willReturn($this->prophesize(ResponseInterface::class))
        ;

        $context = new MockServerContext($client->reveal());

        $context->beforeScenario();
        $context->theRequestOnApiWillReturn('get', '/users', new PyStringNode(['[]'], 0));
        $context->theRequestOnApiWillReturn('get', '/users/1', new PyStringNode(['{}'], 0));
    }

    public function testResetIsNotCalledIfNotApiCall()
    {
        $client = $this->prophesize(MockServerClient::class);

        $client
            ->reset()
            ->shouldBeCalledTimes(0)
            ->willReturn($this->prophesize(ResponseInterface::class))
        ;

        $context = new MockServerContext($client->reveal());

        $context->beforeScenario();
    }
}
