<?php

declare(strict_types=1);

namespace Lequipe\Test\MockServer;

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
        ;

        $client
            ->expectation(Argument::any())
        ;

        $context = new MockServerContext($client->reveal());

        $context->beforeScenario();
        $context->theRequestOnApiWillReturnJson('get', '/users', new PyStringNode(['[]'], 0));
        $context->theRequestOnApiWillReturnJson('get', '/users/1', new PyStringNode(['{}'], 0));
    }

    public function testResetIsNotCalledIfNotApiCall()
    {
        $client = $this->prophesize(MockServerClient::class);

        $client
            ->reset()
            ->shouldBeCalledTimes(0)
        ;

        $context = new MockServerContext($client->reveal());

        $context->beforeScenario();
    }
}
