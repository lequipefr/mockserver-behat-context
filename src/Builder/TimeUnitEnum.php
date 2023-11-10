<?php

declare(strict_types=1);

namespace Lequipe\MockServer\Builder;

use Lequipe\MockServer\Exception\Exception;

/**
 * Time unit, used in http response delay, or time to live.
 */
class TimeUnitEnum
{
    public const DAYS = 'DAYS';
    public const HOURS = 'HOURS';
    public const MINUTES = 'MINUTES';
    public const SECONDS = 'SECONDS';
    public const MILLISECONDS = 'MILLISECONDS';
    public const MICROSECONDS = 'MICROSECONDS';
    public const NANOSECONDS = 'NANOSECONDS';

    public static function all(): array
    {
        return [
            self::DAYS,
            self::HOURS,
            self::MINUTES,
            self::SECONDS,
            self::MILLISECONDS,
            self::MICROSECONDS,
            self::NANOSECONDS,
        ];
    }

    public static function check(string $timeUnit): void
    {
        if (!in_array($timeUnit, self::all())) {
            $join = join(', ', self::all());
            throw new Exception("Invalid timeUnit: '$timeUnit', expected one of: '$join'.");
        }
    }
}
