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
    private ?Delay $delay = null;

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

    public function delay(TimeUnitEnum $timeUnit, int $value): self
    {
        $this->delay = new Delay($timeUnit, $value);

        return $this;
    }

    public function delaySeconds(int $seconds): self
    {
        return $this->delay(TimeUnitEnum::SECONDS, $seconds);
    }

    public function delayMilliseconds(int $milliseconds): self
    {
        return $this->delay(TimeUnitEnum::MILLISECONDS, $milliseconds);
    }

    public function toArray(): array
    {
        return array_filter([
            'statusCode' => $this->statusCode,
            'body' => $this->body,
            'cookies' => $this->cookies,
            'delay' => $this->delay?->toArray(),
        ], function ($v) {
            return null !== $v;
        });
    }
}
