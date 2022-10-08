<?php

namespace abenevaut\Ohdear\Entities;

use abenevaut\Ohdear\Contracts\SiteInterface;
use Spatie\DataTransferObject\DataTransferObject;

class SiteEntity extends DataTransferObject implements SiteInterface
{
    public string $id;

    public function getId(): int
    {
        return $this->id;
    }
}
