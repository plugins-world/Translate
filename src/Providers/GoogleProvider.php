<?php

namespace Yan\Translate\Providers;

use Stichoza\GoogleTranslate\TranslateClient;
use Yan\Translate\Contracts\ProviderInterface;
use Yan\Translate\Exceptions\TranslateException;
use Yan\Translate\Translate;

class GoogleProvider extends AbstractProvider implements ProviderInterface
{
    const HTTP_URL = 'http://translate.google.cn/translate_a/single';

    protected $translateClient;

    protected function getTranslateUrl(): string
    {
        return static::HTTP_URL;
    }

    protected function getRequestParams(array $args) {}

    protected function makeSignature(array $params) {}

    /**
     * {@inheritdoc}
     */
    public function translate(string $q, $from = 'zh-CN', $to = 'en')
    {
        $translateClient = new TranslateClient();
        $translateClient->setSource('zh-CN');
        $translateClient->setTarget('en');
        $translateClient->setUrlBase($this->getTranslateUrl());

        $response = $translateClient->translate($q);
        $rawResponse = $translateClient->getResponse($q);

        return new Translate($this->mapTranslateResult([
            'src' => $q,
            'dst' => $response,
            'original' => $rawResponse,
        ]));
    }

    protected function mapTranslateResult(array $translateResult): array
    {
        return [
            'src' => $translateResult['src'],
            'dst' => $translateResult['dst'],
            'original' => $translateResult['original'],
        ];
    }
}
