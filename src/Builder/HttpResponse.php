<?php

declare(strict_types=1);

namespace Lequipe\MockServer\Builder;

/**
 * A mocked HTTP response stored in MockServer
 * that can be sent back.
 */
class HttpResponse
{
    private ?int $statusCode = null;

    /**
     * @var null|array|string
     */
    private $body = null;

    private ?array $cookies = null;

    public function statusCode(int $statusCode): self
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * @param array|string $body
     */
    public function body($body): self
    {
        $this->body = $body;

        return $this;
    }

    public function bodyJson(array $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function addCookie(string $name, string $value): self
    {
        $this->cookies[$name] = $value;

        return $this;
    }

    public function toArray(): array
    {
        return array_filter([
            'statusCode' => $this->statusCode,
            'body' => $this->body,
            'cookies' => $this->cookies,
        ], function ($v) {
            return null !== $v;
        });
    }
}
