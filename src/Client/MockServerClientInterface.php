<?php

declare(strict_types=1);

namespace Lequipe\MockServer\Client;

interface MockServerClientInterface
{
    /**
     * Puts an expectation on mockserver instance.
     *
     * @param array|Expectation $parameters
     *
     * {@see https://app.swaggerhub.com/apis/jamesdbloom/mock-server-openapi#/expectation/put_expectation}
     */
    public function expectation($parameters): void;

    /**
     * Verify a request has been received a specific number of times.
     *
     * @param array|Verification $parameters
     *
     * {@see https://app.swaggerhub.com/apis/jamesdbloom/mock-server-openapi#/verify/put_mockserver_verify}
     */
    public function verify($parameters): bool;

    /**
     * Clears all expectations and recorded requests.
     *
     * {@see https://app.swaggerhub.com/apis/jamesdbloom/mock-server-openapi#/control/put_mockserver_reset}
     */
    public function reset(): void;
}
