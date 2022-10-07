<?php

namespace abenevaut\Ohdear\Entities;

use abenevaut\Ohdear\Contracts\InvoiceInterface;
use Spatie\DataTransferObject\DataTransferObject;

class UptimeEntity extends DataTransferObject implements InvoiceInterface
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