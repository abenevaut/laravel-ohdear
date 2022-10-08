<?php

namespace abenevaut\Ohdear\Commands;

use abenevaut\Ohdear\Actions\ListUptimeFromPastThreeMonthsAction;
use abenevaut\Ohdear\Actions\ListUptimeFromStartOfWeekAction;
use abenevaut\Ohdear\Contracts\ValidateCommandArgumentsTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use function Termwind\{render};

class ListUptimeCommand extends Command
{
    use ValidateCommandArgumentsTrait;

    protected $signature = 'ohdear:list:uptime
        {from=start_of_week : Display uptime from start of week with option `start_of_week` or past three months with option `past_three_months`}
        {--sites= : Specify sites list, separated by comma}';

    protected $description = 'List OhDear uptime by site';

    public function handle()
    {
        try {
            $this->validate();

            switch ($this->argument('from')) {
                case 'start_of_week':
                    $uptimeAvg = (new ListUptimeFromStartOfWeekAction())
                        ->execute(explode(',', $this->option('sites')))
                        ->uptimes
                        ->avg();
                    $uptimeAvg = round($uptimeAvg, 2);

                    $this->info("Uptime from start of week: {$uptimeAvg}%");
                    break;
                case 'past_three_months':
                    $action = new ListUptimeFromPastThreeMonthsAction();

                    $this
                        ->table(
                            [
                                'Month',
                                'Uptime (%)',
                            ],
                            $action
                                ->uptimes
                                ->map(function ($uptime) {
                                    $avgUptime = round(collect($uptime['uptime_percentage'])->avg(), 2);

                                    return [$uptime['month'], $avgUptime];
                                })
                                ->toArray()
                        );

                    $this->newLine();

                    $action
                        ->uptimes
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

                    break;
            }

            return self::SUCCESS;
        } catch (\Exception $exception) {
            return $this->displayErrors();
        }
    }

    protected function rules(): array
    {
        return [
            'command' => 'required|in:ohdear:list:uptime',
            'from' => 'required|in:start_of_week,past_three_months',
        ];
    }
}
