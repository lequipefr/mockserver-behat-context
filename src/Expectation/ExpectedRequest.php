<?php

declare(strict_types=1);

namespace Lequipe\MockServer\Expectation;

class ExpectedRequest
{
    private ?bool $secure = null;
    private ?string $method = null;
    private ?string $path = null;
    private ?array $queryStringParameters = null;
    private ?array $headers = null;
    private ?array $body = null;

    public function method(string $method): self
    {
        $this->method = $method;

        return $this;
    }

    public function path(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function addHeader(string $name, string $value): self
    {
        if (null === $this->headers) {
            $this->headers = [];
        }

        $this->headers[] = [
            'name' => $name,
            'values' => [$value],
        ];

        return $this;
    }

    public function addQueryStringParameter(string $name, string $value): self
    {
        if (null === $this->queryStringParameters) {
            $this->queryStringParameters = [];
        }

        $this->queryStringParameters[] = [
            'name' => $name,
            'values' => [$value],
        ];

        return $this;
    }

    /**
     * Extract parameters from queryString and expects them all.
     *
     * Example:
     *
     *      $expectedRequest->addQueryStringParametersFromString('param1=value1&list[]=item1&list[]=item2');
     *
     * @param string $queryString String extracted from parseUri()['query'],
     *                            like: 'param1=value1&list[]=item1&list[]=item2'
     */
    public function addQueryStringParametersFromString(string $queryString): self
    {
        parse_str($queryString, $params);

        foreach ($params as $name => $value) {
            $this->addQueryStringParameter($name, $value);
        }

        return $this;
    }

    public function bodyRaw(string $body): self
    {
        $this->body = [
            'string' => $body,
        ];

        return $this;
    }

    public function bodyJson(array $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function secure(bool $secure = true): self
    {
        $this->secure = $secure;

        return $this;
    }

    public function toArray(): array
    {
        return array_filter([
            'secure' => $this->secure,
            'method' => $this->method,
            'path' => $this->path,
            'headers' => $this->headers,
            'body' => $this->body,
        ], function ($v) {
            return null !== $v;
        });
    }
}