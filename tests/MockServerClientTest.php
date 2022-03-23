<?php

declare(strict_types=1);

namespace Lequipe\Test\MockServer;

use Lequipe\MockServer\MockServerClient;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class MockServerClientTest extends TestCase
{
    use ProphecyTrait;

    public function testExpectation()
    {
        $httpClient = $this->prophesize(HttpClientInterface::class);
        $response = $this->prophesize(ResponseInterface::class);

        $json = json_encode([
            'id' => 1,
            'name' => 'Zidane',
        ]);

        $httpClient
            ->request('PUT', '/expectation', [
                'json' => [
                    'httpRequest' => [
                        'method' => 'GET',
                        'path' => '/user/1',
                    ],
                    'httpResponse' => [
                        'body' => $json,
                    ],
                ],
            ])
            ->willReturn($response->reveal())
            ->shouldBeCalled()
        ;

        $client = new MockServerClient($httpClient->reveal());

        $client->expectation([
            'httpRequest' => [
                'method' => 'GET',
                'path' => '/user/1',
            ],
            'httpResponse' => [
                'body' => $json,
            ],
        ]);
    }

    public function testReset()
    {
        $httpClient = $this->prophesize(HttpClientInterface::class);
        $response = $this->prophesize(ResponseInterface::class);

        $httpClient
            ->request('PUT', '/reset')
            ->willReturn($response->reveal())
            ->shouldBeCalled()
        ;

        $client = new MockServerClient($httpClient->reveal());

        $client->reset();
    }
}
