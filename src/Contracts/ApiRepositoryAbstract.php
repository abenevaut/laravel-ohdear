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

    public function __construct(
        private readonly string $accessToken,
        private readonly bool $debug
    ) {
    }

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
    protected function request(array $requestHeaders = []): PendingRequest
    {
        $pendingRequest = $this->withHeaders($requestHeaders);

        if ($this->debug) {
            $pendingRequest->withoutVerifying();
        }

        return $pendingRequest->retry(3, 100);
    }

    /**
     * @return PendingRequest
     */
    private function withHeaders(array $requestHeaders = []): PendingRequest
    {
        $defaultHeaders = [];

        return Http::withHeaders(array_merge($defaultHeaders, $requestHeaders))
            ->acceptJson();
    }
}
