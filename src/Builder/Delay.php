<?php

declare(strict_types=1);

namespace Lequipe\MockServer\Builder;

class Delay
{
    public function __construct(
        private TimeUnitEnum $timeUnit,
        private int $value,
    ) {}

    public function getTimeUnit(): TimeUnitEnum
    {
        return $this->timeUnit;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function toArray(): array
    {
        return [
            'timeUnit' => $this->timeUnit->name,
            'value' => $this->value,
        ];
    }
}
