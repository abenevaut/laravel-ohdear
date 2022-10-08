<?php

namespace abenevaut\Ohdear\Commands;

use abenevaut\Ohdear\Contracts\OhdearDriversEnum;
use abenevaut\Ohdear\Entities\SiteEntity;
use abenevaut\Ohdear\Facades\Ohdear;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

use function Termwind\{render};

class ListUptimeCommand extends Command
{
    protected $signature = 'ohdear:list:uptime
        {from=start_of_week : Display uptime from start of week with option `start_of_week` or past three months with option `past_three_months`}
        {--sites= : Specify sites list, separated by comma}';

    protected $description = 'List OhDear uptime by site';

    public function handle()
    {
        if (in_array($this->argument('from'), ['start_of_week', 'past_three_months']) === false) {
            $this->error('Unavailable argument "from"!');

            return self::FAILURE;
        }

        switch ($this->argument('from')) {
            case 'start_of_week':
                $this->startOfWeek();
                break;
            case 'past_three_months':
                $this->pastThreeMonths();
                break;
        }

        return self::SUCCESS;
    }

    private function startOfWeek()
    {
        $sitePage = 0;
        $uptimes = collect();
        $startOfWeek = Carbon::now()->startOfWeek();
        $now = Carbon::now()->subHour();

        do {
            $sitePage += 1;
            /** @var \Illuminate\Pagination\LengthAwarePaginator $sites */
            $sites = Ohdear::request(OhdearDriversEnum::SITES)
                ->all($sitePage);

            $sites
                ->each(function (SiteEntity $site) use ($uptimes, $now, $startOfWeek) {
                    if (in_array($site->getId(), explode(',', $this->option('sites'))) === false) {
                        return;
                    }

                    $uptime = Ohdear::request(OhdearDriversEnum::SITES)
                        ->getUptime(
                            $site->getId(),
                            $startOfWeek->format('YmdHis'),
                            $now->format('YmdHis')
                        );

                    if ($uptime) {
                        $uptimes->push($uptime->uptime_percentage);
                    }
                });
        } while ($sites->isNotEmpty() && $sites->hasMorePages());

        $uptimeAvg = round($uptimes->avg(), 2);

        $this->info("Uptime from start of week: {$uptimeAvg}%");
    }

    private function pastThreeMonths()
    {
        $sitePage = 0;
        $uptimes = collect();

        do {
            $sitePage += 1;
            /** @var \Illuminate\Pagination\LengthAwarePaginator $sites */
            $sites = Ohdear::request(OhdearDriversEnum::SITES)
                ->all($sitePage);

            for ($i = 2; $i >= 0; $i--) {
                $currentMonth = CarbonImmutable::now()->subMonthsNoOverflow($i);
                $startOfMonth = $currentMonth->startOfMonth();
                $endOfMonth = $currentMonth->endOfMonth();

                if ($endOfMonth->isFuture() === true) {
                    $endOfMonth = Carbon::now()->subDay()->endOfDay();
                }

                if ($endOfMonth->isAfter($startOfMonth) === false) {
                    continue;
                }

                $sites
                    ->each(function (SiteEntity $site) use ($uptimes, $currentMonth, $startOfMonth, $endOfMonth) {
                        if (in_array($site->getId(), explode(',', $this->option('sites'))) === false) {
                            return;
                        }

                        $uptime = Ohdear::request(OhdearDriversEnum::SITES)
                            ->getUptime(
                                $site->getId(),
                                $startOfMonth->format('YmdHis'),
                                $endOfMonth->format('YmdHis')
                            );

                        if ($uptime) {
                            $uptimes->push([
                                'month' => $currentMonth->monthName,
                                'site' => $site->getId(),
                                'uptime_percentage' => $uptime->uptime_percentage,
                            ]);
                        }
                    });
            }
        } while ($sites->isNotEmpty() && $sites->hasMorePages());

        $this
            ->table(
                [
                    'Month',
                    'Uptime (%)',
                ],
                $uptimes
                    ->map(function ($uptime) {
                        $avgUptime = round(collect($uptime['uptime_percentage'])->avg(), 2);

                        return [$uptime['month'], $avgUptime];
                    })
                    ->toArray()
            );

        $this->newLine();

        $uptimes
            ->groupBy('month')
            ->each(function (Collection $data) {
                $this
                    ->table(
                        [
                            'Month',
                            'Site',
                            'Uptime (%)',
                        ],
                        $data
                            ->sortBy('site')
                            ->values()
                            ->toArray()
                    );
            });
    }
}
