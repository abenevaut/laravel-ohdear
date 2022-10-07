<?php

namespace abenevaut\Ohdear\Entities;

use abenevaut\Ohdear\Contracts\UptimeInterface;
use Spatie\DataTransferObject\DataTransferObject;

class UptimeEntity extends DataTransferObject implements UptimeInterface
{
    public string $datetime;

    public string $uptimePercentage;

    public function getUptimePercentage(): string
    {
        return $this->uptimePercentage;
    }

    public function getDatetime(): string
    {
        return $this->datetime;
    }
}