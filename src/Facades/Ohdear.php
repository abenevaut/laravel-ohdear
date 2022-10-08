<?php

namespace abenevaut\Ohdear\Facades;

use abenevaut\Ohdear\Contracts\OhdearProviderNameInterface;
use abenevaut\Ohdear\Entities\SiteEntity;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Http;

class Ohdear extends Facade implements OhdearProviderNameInterface
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return self::OHDEAR;
    }

    /**
     * https://ohdear.app/docs/integrations/the-oh-dear-api#sites
     *
     * @return void
     */
    public static function fakeSitesHttp(int $page = 1, int $total = 2, int $perPage = 5)
    {
        $data = [];
        $sitesParams = http_build_query([
            'page[number]' => $page,
        ]);

        for ($i = ($total > $perPage ? $perPage : $total); $i > 0; --$i) {
            $data[] = SiteEntity::factory()->make();
        }

        Http::fake([
            "https://ohdear.app/api/sites?{$sitesParams}" => Http::response([
                "data" => $data,
                "meta" => [
                    "total" => $total,
                    "per_page" => $perPage,
                    "current_page" => $page,
                ]
            ]),
        ]);
    }
}
