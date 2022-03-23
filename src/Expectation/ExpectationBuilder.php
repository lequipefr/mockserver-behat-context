<?php

declare(strict_types=1);

namespace Lequipe\MockServer\Expectation;

use Lequipe\MockServer\Expectation\ExpectedRequest;
use Lequipe\MockServer\Expectation\MockedResponse;

class ExpectationBuilder
{
    private ExpectedRequest $expectedRequest;
    private MockedResponse $mockedResponse;

    public function __construct()
    {
        $this->expectedRequest = new ExpectedRequest();
        $this->mockedResponse = new MockedResponse();
    }

    public function expectedRequest(): ExpectedRequest
    {
        return $this->expectedRequest;
    }

    public function mockedResponse(): MockedResponse
    {
        return $this->mockedResponse;
    }

    public function toArray(): array
    {
        return array_filter([
            'httpRequest' => $this->expectedRequest->toArray(),
            'httpResponse' => $this->mockedResponse->toArray(),
        ], function ($v) {
            return null !== $v;
        });
    }
}
