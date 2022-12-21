<?php

declare(strict_types=1);

namespace Lequipe\Test\MockServer;

use Lequipe\MockServer\Builder\Expectation;
use PHPUnit\Framework\TestCase;

class ExpectationTest extends TestCase
{
    public function testExpectation(): void
    {
        $builder = new Expectation();

        $builder->httpRequest()
            ->method('post')
            ->path('api/users')
            ->addHeader('Content', 'application/form-data')
            ->bodyJson([
                'name' => 'Zidane',
            ])
        ;

        $builder->httpResponse()
            ->statusCode(201)
            ->bodyJson([
                'id' => 1,
                'name' => 'Zidane',
            ])
        ;

        $parameters = $builder->toArray();

        $this->assertEquals([
            'httpRequest' => [
                'method' => 'post',
                'path' => 'api/users',
                'headers' => [
                    ['name' => 'Content', 'values' => ['application/form-data']],
                ],
                'body' => [
                    'name' => 'Zidane',
                ],
            ],
            'httpResponse' => [
                'statusCode' => 201,
                'body' => [
                    'id' => 1,
                    'name' => 'Zidane',
                ],
            ],
        ], $parameters);
    }

    public function testExpectationQueryString(): void
    {
        $builder = new Expectation();

        $builder->httpRequest()
            ->method('get')
            ->path('api/users')
            ->addQueryStringParametersFromString('filter=enabled&site=lequipe')
        ;

        $parameters = $builder->toArray();

        $this->assertEquals([
            'httpRequest' => [
                'method' => 'get',
                'path' => 'api/users',
                'queryStringParameters' => [
                    ['name' => 'filter', 'values' => ['enabled']],
                    ['name' => 'site', 'values' => ['lequipe']],
                ],
            ],
        ], $parameters);
    }
}
