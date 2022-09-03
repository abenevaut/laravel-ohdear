<?php

namespace abenevaut\Ohdear\Contracts;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

abstract class ApiRepositoryAbstract
{
    /**
     * @var string
     */
    private string $baseUrl = 'https://ohdear.app/api';

    /**
     * @param  string  $uri
     * @return string
     */
    protected function makeUrl(string $uri): string
    {
        return "{$this->baseUrl}{$uri}";
    }

    /**
     * @return PendingRequest
     */
    protected function request(): PendingRequest
    {
        return $this
            ->withHeaders()
            ->retry(3, 100);
    }

    /**
     * @return PendingRequest
     */
    private function withHeaders(): PendingRequest
    {
        return Http::withToken(config('ohdear.access_token'))
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]);
    }
}
