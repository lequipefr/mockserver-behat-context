<?php

declare(strict_types=1);

namespace Lequipe\MockServer\Builder;

class Verification
{
    private HttpRequest $httpRequest;
    private ?int $atLeast = null;
    private ?int $atMost = null;

    public function __construct()
    {
        $this->httpRequest = new HttpRequest();
    }

    public function httpRequest(): HttpRequest
    {
        return $this->httpRequest;
    }

    public function atLeast(int $atLeast): self
    {
        $this->atLeast = $atLeast;

        return $this;
    }

    public function atMost(int $atMost): self
    {
        $this->atMost = $atMost;

        return $this;
    }

    public function between(int $least, int $most): self
    {
        $this->atLeast = $least;
        $this->atMost = $most;

        return $this;
    }

    public function exactly(int $times): self
    {
        $this->atLeast = $times;
        $this->atMost = $times;

        return $this;
    }

    public function toArray(): array
    {
        return array_filter([
            'httpRequest' => $this->httpRequest->toArray(),
            'times' => [
                'atLeast' => $this->atLeast,
                'atMost' => $this->atMost,
            ],
        ], function ($v) {
            return null !== $v;
        });
    }
}
