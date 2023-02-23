<?php

declare(strict_types=1);

namespace Lequipe\Test\MockServer\Builder;

use Lequipe\MockServer\Builder\Verification;
use PHPUnit\Framework\TestCase;

use function is_array;
use function ksort;

class VerificationTest extends TestCase
{
    /**
     * Mockserver documentation example,
     * "verify requests"
     */
    public function testDocVerifyRequests(): void
    {
        $verification = new Verification();

        $verification->httpRequest()
            ->path('/simple')
        ;

        $verification->exactly(2);

        $this->assertSameArray([
            'httpRequest' => [
                'path' => '/simple',
            ],
            'times' => [
                'atLeast' => 2,
                'atMost' => 2,
            ],
        ], $verification->toArray());
    }

    private function assertSameArray(array $expected, array $actual, string $message = ''): void
    {
        self::ksortDeep($expected);
        self::ksortDeep($actual);

        $this->assertSame($expected, $actual, $message);
    }

    private static function ksortDeep(array &$array): void
    {
        ksort($array);

        foreach ($array as &$subArray) {
            if (is_array($subArray)) {
                self::ksortDeep($subArray);
            }
        }
    }
}
