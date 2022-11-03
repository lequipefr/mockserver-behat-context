<?php

declare(strict_types=1);

namespace Lequipe\Test\MockServer\Client;

use Lequipe\MockServer\Client\MockServerClient;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class MockServerClientTest extends TestCase
{
    use ProphecyTrait;

    public function testExpectation()
    {
        $client = $this->prophesize(ClientInterface::class);
        $response = $this->prophesize(ResponseInterface::class);

        $expectationPayload = [
            'httpRequest' => [
                'method' => 'GET',
                'path' => '/user/1',
            ],
            'httpResponse' => [
                'body' => [
                    'id' => 1,
                    'name' => 'Zidane',
                ],
            ],
        ];

        $client
            ->sendRequest(Argument::that(fn (RequestInterface $request) =>
                $request->getMethod() === 'PUT'
                && $request->getUri()->__toString() === 'https://127.0.0.1:8080/expectation'
                && $request->getHeader('content-type') === ['application/json']
                && $request->getBody()->getContents() === json_encode($expectationPayload)
            ))
            ->willReturn($response->reveal())
            ->shouldBeCalled()
        ;

        $client = new MockServerClient('https://127.0.0.1:8080', $client->reveal());

        $client->expectation($expectationPayload);
    }

    public function testExpectationWithPrefixedBaseUri()
    {
        $client = $this->prophesize(ClientInterface::class);
        $response = $this->prophesize(ResponseInterface::class);

        $expectationPayload = [
            'httpRequest' => [
                'method' => 'GET',
                'path' => '/user/1',
            ],
            'httpResponse' => [
                'body' => [
                    'id' => 1,
                    'name' => 'Zidane',
                ],
            ],
        ];

        $client
            ->sendRequest(Argument::that(function (RequestInterface $request) use ($expectationPayload) {
                $this->assertEquals('PUT', $request->getMethod());
                $this->assertEquals('https://mockserver.tld/prefix/expectation', $request->getUri()->__toString());
                $this->assertEquals(['application/json'], $request->getHeader('content-type'));
                $this->assertEquals(json_encode($expectationPayload), $request->getBody()->getContents());

                return true;
            }))
            ->willReturn($response->reveal())
            ->shouldBeCalled()
        ;

        $client = new MockServerClient('https://mockserver.tld/prefix/', $client->reveal());

        $client->expectation($expectationPayload);
    }

    public function testReset()
    {
        $client = $this->prophesize(ClientInterface::class);
        $response = $this->prophesize(ResponseInterface::class);

        $client
            ->sendRequest(Argument::that(function (RequestInterface $request) {
                $this->assertEquals('PUT', $request->getMethod());
                $this->assertEquals('https://127.0.0.1/reset', $request->getUri()->__toString());
                $this->assertEquals('', $request->getBody()->getContents());

                return true;
            }))
            ->willReturn($response->reveal())
            ->shouldBeCalled()
        ;

        $client = new MockServerClient('https://127.0.0.1', $client->reveal());

        $client->reset();
    }
}
