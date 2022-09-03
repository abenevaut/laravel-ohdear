<?php

namespace abenevaut\Ohdear\Repositories;

use abenevaut\Ohdear\Contracts\ApiRepositoryAbstract;
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

    /**
     * @param  int  $siteId
     * @param  string  $startedAt
     * @param  string  $endedAt
     * @param  string  $split
     * @return Collection
     */
    public function getUptime(int $siteId, string $startedAt, string $endedAt, string $split = 'month'): Collection
    {
        $params = http_build_query([
            'filter[started_at]' => $startedAt,
            'filter[ended_at]' => $endedAt,
            'split' => $split,
        ]);

        return $this
            ->request()
            ->get($this->makeUrl("/sites/{$siteId}/uptime?{$params}"))
            ->collect();
    }
}
