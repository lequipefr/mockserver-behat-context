<?php

declare(strict_types=1);

namespace Lequipe\MockServer\Builder;

/**
 * Time unit, used in http response delay, or time to live.
 */
enum TimeUnitEnum
{
    case DAYS;
    case HOURS;
    case MINUTES;
    case SECONDS;
    case MILLISECONDS;
    case MICROSECONDS;
    case NANOSECONDS;
}
