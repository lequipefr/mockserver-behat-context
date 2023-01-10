<?php

declare(strict_types=1);

namespace Lequipe\MockServer\Builder;

use Lequipe\MockServer\Builder\HttpRequest;
use Lequipe\MockServer\Builder\HttpResponse;

class Expectation
{
    private HttpRequest $httpRequest;
    private HttpResponse $httpResponse;
    private Times $times;

    public function __construct()
    {
        $this->httpRequest = new HttpRequest();
        $this->httpResponse = new HttpResponse();
        $this->times = new Times();
    }

    public function httpRequest(): HttpRequest
    {
        return $this->httpRequest;
    }

    public function httpResponse(): HttpResponse
    {
        return $this->httpResponse;
    }

    public function times(): Times
    {
        return $this->times;
    }

    public function toArray(): array
    {
        return array_filter([
            'httpRequest' => $this->httpRequest->toArray(),
            'httpResponse' => $this->httpResponse->toArray(),
            'times' => $this->times->toArray(),
        ]);
    }
}
