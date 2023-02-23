<?php

declare(strict_types=1);

namespace Lequipe\Test\MockServer\Builder;

use Lequipe\MockServer\Builder\Expectation;
use PHPUnit\Framework\TestCase;

use function is_array;
use function ksort;

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

        $this->assertSameArray([
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

        $this->assertSameArray([
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

    public function testLimitTheNumberOfTimesAnExpectationIsUsed(): void
    {
        $expectation = new Expectation();

        $expectation->httpRequest()
            ->method('delete')
            ->path('/api/users/1')
        ;

        $expectation->httpResponse()
            ->bodyJson(['message' => 'deleted'])
        ;

        $expectation->times()->setRemainingTimes(2);

        $this->assertSameArray([
            'httpRequest' => [
                'method' => 'delete',
                'path' => '/api/users/1',
            ],
            'httpResponse' => [
                'body' => [
                    'message' => 'deleted',
                ],
            ],
            'times' => [
                'remainingTimes' => 2,
            ],
        ], $expectation->toArray());
    }

    public function testOnce(): void
    {
        $expectation = new Expectation();

        $expectation->httpRequest()
            ->method('delete')
            ->path('/api/users/1')
        ;

        $expectation->httpResponse()
            ->bodyJson(['message' => 'deleted'])
        ;

        $expectation->times()->once();

        $this->assertSameArray([
            'httpRequest' => [
                'method' => 'delete',
                'path' => '/api/users/1',
            ],
            'httpResponse' => [
                'body' => [
                    'message' => 'deleted',
                ],
            ],
            'times' => [
                'remainingTimes' => 1,
                'unlimited' => false,
            ],
        ], $expectation->toArray());
    }

    /**
     * Mockserver documentation example,
     * "create expectation"
     */
    public function testDocCreateExpectation(): void
    {
        $expectation = new Expectation();

        $expectation->httpRequest()
            ->method('get')
            ->pathWithParameters('/view/cart?cartId=055CA455-1DF7-45BB-8535-4F83E7266092')
            ->addCookie('session', '4930456C-C718-476F-971F-CB8E047AB349')
        ;

        $expectation->httpResponse()
            ->body('some_response_body')
        ;

        $this->assertSameArray([
            'httpRequest' => [
                'method' => 'get',
                'path' => '/view/cart',
                'queryStringParameters' => [
                    [
                        'name' => 'cartId',
                        'values' => [
                            '055CA455-1DF7-45BB-8535-4F83E7266092',
                        ],
                    ],
                ],
                'cookies' => [
                    [
                        'name' => 'session',
                        'value' => '4930456C-C718-476F-971F-CB8E047AB349',
                    ],
                ],
            ],
            'httpResponse' => [
                'body' => 'some_response_body',
            ],
        ], $expectation->toArray());
    }

    private function assertSameArray(array $expected, array $actual, string $message = ''): void
    {
        self::ksortDeep($expected);
        self::ksortDeep($actual);

        $this->assertSame($expected, $actual, $message);
    }

    private static function ksortDeep(array &$array): void
    {
        ksort($array);

        foreach ($array as &$subArray) {
            if (is_array($subArray)) {
                self::ksortDeep($subArray);
            }
        }
    }
}
