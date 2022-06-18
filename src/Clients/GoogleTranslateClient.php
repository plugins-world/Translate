<?php

namespace MouYong\Translate\Clients;

use ArrayAccess;
use ZhenMu\Support\Traits\Clientable;
use MouYong\Translate\Supports\Config;
use ZhenMu\Support\Traits\DefaultClient;
use Stichoza\GoogleTranslate\GoogleTranslate;

class GoogleTranslateClient extends GoogleTranslate implements ArrayAccess
{
    use Clientable;
    use DefaultClient;

    protected ?Config $config;

    public function __construct(?Config $config, ...$params)
    {
        parent::__construct(...$params);

        $this->client = $this;

        $this->config = $config;
    }

    public function getOptions()
    {
        $http = $this->config['http']?->toArray() ?? [];

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
