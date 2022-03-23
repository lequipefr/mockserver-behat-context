<?php

declare(strict_types=1);

namespace Lequipe\MockServer\Expectation;

class MockedResponse
{
    private ?int $statusCode = null;
    private ?array $body = null;

    public function statusCode(int $statusCode): self
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    public function bodyJson(array $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function toArray(): array
    {
        return array_filter([
            'statusCode' => $this->statusCode,
            'body' => $this->body,
        ], function ($v) {
            return null !== $v;
        });
    }
}
