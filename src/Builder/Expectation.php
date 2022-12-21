<?php

declare(strict_types=1);

namespace Lequipe\MockServer\Builder;

use Lequipe\MockServer\Builder\HttpRequest;
use Lequipe\MockServer\Builder\HttpResponse;

class Expectation
{
    private HttpRequest $httpRequest;
    private HttpResponse $httpResponse;

    public function __construct()
    {
        $this->httpRequest = new HttpRequest();
        $this->httpResponse = new HttpResponse();
    }

    public function httpRequest(): HttpRequest
    {
        return $this->httpRequest;
    }

    public function httpResponse(): HttpResponse
    {
        return $this->httpResponse;
    }

    public function toArray(): array
    {
        return array_filter([
            'httpRequest' => $this->httpRequest->toArray(),
            'httpResponse' => $this->httpResponse->toArray(),
        ]);
    }
}
