<?php

namespace abenevaut\Ohdear\Repositories;

use abenevaut\Ohdear\Contracts\ApiRepositoryAbstract;
use abenevaut\Ohdear\Entities\UptimeEntity;
use Illuminate\Support\Collection;

final class SitesRepository extends ApiRepositoryAbstract
{
    /**
     * @return Collection
     */
    public function all(): Collection
    {
        return $this
            ->request()
            ->get($this->makeUrl("/sites"))
            ->collect();
    }

    public function getUptime(int $siteId, string $startedAt, string $endedAt, string $split = 'month'): UptimeEntity
    {
        $params = http_build_query([
            'filter[started_at]' => $startedAt,
            'filter[ended_at]' => $endedAt,
            'split' => $split,
        ]);

        $resource = $this
            ->request()
            ->get($this->makeUrl("/sites/{$siteId}/uptime?{$params}"))
            ->json();

        return new UptimeEntity($resource);
    }
}
