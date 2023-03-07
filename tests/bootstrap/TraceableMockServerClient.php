<?php

declare(strict_types=1);

namespace Lequipe\Test\MockServer;

use Lequipe\MockServer\Builder\Expectation;
use Lequipe\MockServer\Builder\Verification;
use Lequipe\MockServer\Client\MockServerClientInterface;
use Lequipe\MockServer\Utils;

/**
 * Fake mockserver client that stores requests in php array instead of sending to a mockserver.
 * Requests can be retrieved with getters, returning parameters that would have been sent to mockserver.
 */
class TraceableMockServerClient implements MockServerClientInterface
{
    private array $expectations = [];
    private array $verifications = [];
    private int $resetCallsCount = 0;

    /**
     * {@inheritDoc}
     */
    public function expectation($parameters): void
    {
        $this->expectations[] = Utils::toArray($parameters, Expectation::class);
    }

    public function getExpectations(): array
    {
        return $this->expectations;
    }

    /**
     * {@inheritDoc}
     */
    public function verify($parameters): bool
    {
        $this->verifications[] = Utils::toArray($parameters, Verification::class);

        return true;
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
        $this->expectations = [];
        $this->verifications = [];
        ++$this->resetCallsCount;
    }

    public function getResetCallsCount(): int
    {
        return $this->resetCallsCount;
    }

    public function resetResetCallsCount(): void
    {
        $this->resetCallsCount = 0;
    }
}
