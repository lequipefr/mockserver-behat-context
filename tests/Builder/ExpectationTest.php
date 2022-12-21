<?php

declare(strict_types=1);

namespace Lequipe\Test\MockServer\Builder;

use Lequipe\MockServer\Builder\Expectation;
use PHPUnit\Framework\TestCase;

class ExpectationTest extends TestCase
{
    public function testSimpleJsonRequest(): void
    {
        $expectation = new Expectation();

        $expectation->httpRequest()
            ->method('get')
            ->path('/api/users')
        ;

        $expectation->httpResponse()
            ->bodyJson([
                [
                    'id' => 1,
                    'name' => 'Zidane',
                ],
            ])
        ;

        $this->assertEqualsCanonicalizing([
            'httpRequest' => [
                'method' => 'get',
                'path' => '/api/users',
            ],
            'httpResponse' => [
                'body' => [
                    [
                        'id' => 1,
                        'name' => 'Zidane',
                    ],
                ],
            ],
        ], $expectation->toArray());
    }

    public function testCookies(): void
    {
        $expectation = new Expectation();

        $expectation->httpRequest()
            ->method('get')
            ->path('/api/users')
            ->addCookie('sessionId', '123abc')
        ;

        $expectation->httpResponse()
            ->addCookie('sessionId', '456def')
        ;

        $this->assertEqualsCanonicalizing([
            'httpRequest' => [
                'method' => 'get',
                'path' => '/api/users',
                'cookies' => [
                    [
                        'name' => 'sessionId',
                        'value' => '123abc',
                    ],
                ],
            ],
            'httpResponse' => [
                'cookies' => [
                    'sessionId' => '456def',
                ],
            ],
        ], $expectation->toArray());
    }
}
