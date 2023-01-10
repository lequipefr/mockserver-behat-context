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
