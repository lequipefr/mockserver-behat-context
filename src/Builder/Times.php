<?php

declare(strict_types=1);

namespace Lequipe\MockServer\Builder;

class Times
{
    private ?int $remainingTimes = null;
    private ?bool $unlimited = null;

    public function getRemainingTimes(): ?int
    {
        return $this->remainingTimes;
    }

    public function setRemainingTimes(?int $remainingTimes): self
    {
        $this->remainingTimes = $remainingTimes;

        return $this;
    }

    public function getUnlimited(): ?bool
    {
        return $this->unlimited;
    }

    public function setUnlimited(?bool $unlimited): self
    {
        $this->unlimited = $unlimited;

        return $this;
    }

    public function once(): self
    {
        $this->remainingTimes = 1;
        $this->unlimited = false;

        return $this;
    }

    public function toArray(): array
    {
        return array_filter([
            'remainingTimes' => $this->remainingTimes,
            'unlimited' => $this->unlimited,
        ], function ($v) {
            return null !== $v;
        });
    }
}
