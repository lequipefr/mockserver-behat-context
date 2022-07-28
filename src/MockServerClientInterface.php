<?php

declare(strict_types=1);

namespace Lequipe\MockServer;

interface MockServerClientInterface
{
    /**
     * Puts an expectation on mockserver instance.
     *
     * {@see https://app.swaggerhub.com/apis/jamesdbloom/mock-server-openapi/5.13.x#/expectation/put_expectation}
     */
    public function expectation(array $parameters): void;

    /**
     * Verify a request has been received a specific number of times.
     *
     * {@see https://app.swaggerhub.com/apis/jamesdbloom/mock-server-openapi/5.13.x#/verify/put_mockserver_verify}
     */
    public function verify(array $parameters): void;

    /**
     * Clears all expectations and recorded requests.
     *
     * {@see https://app.swaggerhub.com/apis/jamesdbloom/mock-server-openapi/5.13.x#/control/put_mockserver_reset}
     */
    public function reset(): void;
}
