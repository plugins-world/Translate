<?php

namespace Plugins\Translate\Core\Traits;

use GuzzleHttp\Client;
use Psr\Http\Client\ClientInterface;

trait InteractWithHttpClient
{
    protected ?Client $httpClient = null;

    public function getHttpClient(): Client
    {
        if (! $this->httpClient) {
            $this->httpClient = $this->createHttpClient();
        }

        return $this->httpClient;
    }

    protected function createHttpClient(): ClientInterface|Client
    {
        $options = $this->getHttpClientDefaultOptions();

        return new Client($options);
    }

    protected function getHttpClientDefaultOptions(): array
    {
        return [];
    }
}
