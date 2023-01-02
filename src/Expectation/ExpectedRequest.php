<?php

declare(strict_types=1);

namespace Lequipe\MockServer\Expectation;

use Lequipe\MockServer\Utils;

use function array_filter;
use function array_slice;
use function is_string;
use function count;

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
        Utils::parse_str_custom($queryString, $params);

        $this->recursiveAddQueryString($params);

        return $this;
    }

    /**
     * Handle deep arrays in query strings, like '?param[x][y][z]=value'
     *
     * @param array $params Result from parse_str() function
     * @param string[] $tree Current array path, like ['param', 'x', 'y'] Let empty for root.
     */
    private function recursiveAddQueryString(array $params, array $tree = []): void
    {
        foreach ($params as $name => $value) {
            $currentTree = $tree;
            $currentTree[] = $name;

            if (is_string($value)) {
                $parameterName = $currentTree[0];

                if (count($currentTree) > 1) {
                    $parameterName .= '[' . join('][', array_slice($currentTree, 1)) . ']';
                }

                $this->addQueryStringParameter($parameterName, $value);
            } else {
                $this->recursiveAddQueryString($value, $currentTree);
            }
        }
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
            'queryStringParameters' => $this->queryStringParameters,
        ], function ($v) {
            return null !== $v;
        });
    }
}
