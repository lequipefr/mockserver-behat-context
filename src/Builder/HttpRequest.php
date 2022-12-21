<?php

declare(strict_types=1);

namespace Lequipe\MockServer\Builder;

use Lequipe\MockServer\Utils;

use function array_key_exists;
use function array_filter;
use function is_array;
use function parse_url;

/**
 * A request matcher.
 * Can match or not a request received by MockServer.
 */
class HttpRequest
{
    private ?bool $secure = null;
    private ?string $method = null;
    private ?string $path = null;
    private ?array $queryStringParameters = null;
    private ?array $headers = null;
    private ?array $cookies = null;
    private ?array $body = null;

    public function method(string $method): self
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Expects a path only, do not provide query parameters here.
     *
     *  pathWithParameters('/api/users')
     *
     * @see \Lequipe\MockServer\Builder\HttpRequest::pathWithParameters() If you want to provide a path like '/api/users?visible=true'
     * @see \Lequipe\MockServer\Builder\HttpRequest::addQueryStringParameter() If you want to provide query parameters only
     */
    public function path(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Expects a path containing parameters, i.e
     *
     *  pathWithParameters('/api/users?visible=true')
     */
    public function pathWithParameters(string $path): self
    {
        $parsedUrl = parse_url($path);

        if (array_key_exists('query', $parsedUrl)) {
            $this->addQueryStringParametersFromString($parsedUrl['query']);
        }

        $this->path = $parsedUrl['path'];

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

    public function addCookie(string $name, string $value): self
    {
        $this->cookies[] = [
            'name' => $name,
            'value' => $value,
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
     *      $httpRequest->addQueryStringParametersFromString('param1=value1&list[]=item1&list[]=item2');
     *
     * @param string $queryString String extracted from parseUri()['query'],
     *                            like: 'param1=value1&list[]=item1&list[]=item2'
     */
    public function addQueryStringParametersFromString(string $queryString): self
    {
        Utils::parse_str_custom($queryString, $params);

        foreach ($params as $name => $value) {

            if (is_array($value)) {
                foreach ($value as $key => $val) {
                    $this->addQueryStringParameter($name."[".$key."]", $val);
                }
                continue;
            }

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
            'cookies' => $this->cookies,
            'body' => $this->body,
            'queryStringParameters' => $this->queryStringParameters,
        ], function ($v) {
            return null !== $v;
        });
    }
}
