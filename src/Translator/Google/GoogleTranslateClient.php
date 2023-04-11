<?php

namespace MouYong\Translate\Translator\Google;

use Stichoza\GoogleTranslate\GoogleTranslate;

class GoogleTranslateClient extends GoogleTranslate
{
    protected array $config = [];

    public function __construct(array $config = [], mixed ...$params)
    {
        parent::__construct(...$params);

        // 覆盖 options 配置
        $this->config = $config;
    }

    public function resetOptions()
    {
        $http = $this->config['http'] ?? [];

        $options = array_merge([
            'base_uri' => $http['base_uri'] ?? '',
            'timeout' => 5, // 请求 5s 超时
            'http_errors' => false,
            'headers' => [
                'Accept' => 'application/json',
            ],
        ], $http);

        $this->options = $options;

        return $this;
    }
}
