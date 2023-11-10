<?php

declare(strict_types=1);

namespace Lequipe\MockServer\Builder;

class Delay
{
    public function __construct(
        private string $timeUnit,
        private int $value,
    ) {
        TimeUnitEnum::check($timeUnit);
    }

    public function getTimeUnit(): string
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
            'timeUnit' => $this->timeUnit,
            'value' => $this->value,
        ];
    }
}
