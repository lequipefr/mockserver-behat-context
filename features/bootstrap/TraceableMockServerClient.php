<?php

declare(strict_types=1);

use Lequipe\MockServer\MockServerClientInterface;

class TraceableMockServerClient implements MockServerClientInterface
{
    private array $expectations = [];

    /**
     * {@inheritDoc}
     */
    public function expectation(array $parameters): void
    {
        $this->expectations[] = $parameters;
    }

    public function getExpectations(): array
    {
        return $this->expectations;
    }

    /**
     * {@inheritDoc}
     */
    public function verify(array $parameters): void
    {

    }

    /**
     * {@inheritDoc}
     */
    public function reset(): void
    {
    }
}
