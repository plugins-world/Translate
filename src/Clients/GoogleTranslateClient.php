<?php

namespace MouYong\Translate\Clients;

use ZhenMu\Support\Traits\Clientable;
use MouYong\Translate\Supports\Config;
use Stichoza\GoogleTranslate\GoogleTranslate;

class GoogleTranslateClient extends GoogleTranslate
{
    use Clientable;

    protected ?Config $config;

    public function __construct(?Config $config, mixed ...$params)
    {
        parent::__construct(...$params);

        $this->client = $this->getHttpClient();

        $this->config = $config;
    }

    public function getOptions()
    {
        $http = $this->config['http'] ?? [];

        $options = array_merge([
            'base_uri' => $this->getBaseUri(),
            'timeout' => 5, // 请求 5s 超时
            'http_errors' => false,
            'headers' => [
                'Accept' => 'application/json',
            ],
        ], $http);

        return $options;
    }
}
