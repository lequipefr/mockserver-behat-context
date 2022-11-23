<?php

declare(strict_types=1);

use Lequipe\MockServer\MockServerClientInterface;

/**
 * Fake mockserver client that stores requests in php array instead of sending to a mockserver.
 * Requests can be retrieved with getters, returning parameters that would have been sent to mockserver.
 */
class TraceableMockServerClient implements MockServerClientInterface
{
    private array $expectations = [];
    private array $verifications = [];

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
        $this->verifications[] = $parameters;
    }

    public function getVerifications(): array
    {
        return $this->verifications;
    }

    /**
     * {@inheritDoc}
     */
    public function reset(): void
    {
    }
}
