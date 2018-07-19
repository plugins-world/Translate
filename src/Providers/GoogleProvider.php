<?php

namespace Yan\Translate\Providers;

use Stichoza\GoogleTranslate\TranslateClient;
use Yan\Translate\Contracts\ProviderInterface;
use Yan\Translate\Translate;

class GoogleProvider extends AbstractProvider implements ProviderInterface
{
    const HTTP_URL = 'http://translate.google.cn/translate_a/single';

    protected $translateClient;

    protected function getTranslateUrl()
    {
        return static::HTTP_URL;
    }

    /**
     * {@inheritdoc}
     */
    public function translate($q, $from = 'zh-CN', $to = 'en')
    {
        $translateClient = $this->getTranslateClient($from, $to);

        $response = $translateClient->translate($q);
        $rawResponse = $translateClient->getResponse($q);

        return new Translate($this->mapTranslateResult([
            'src' => $q,
            'dst' => $response,
            'original' => $rawResponse,
        ]));
    }

    /**
     * @param string $from
     * @param string $to
     *
     * @return \Stichoza\GoogleTranslate\TranslateClient
     *
     * @throws \Exception
     */
    public function getTranslateClient($from, $to)
    {
        $translateClient = new TranslateClient();

        return $translateClient->setSource($from)
                               ->setTarget($to)
                               ->setUrlBase($this->getTranslateUrl());
    }

    protected function mapTranslateResult(array $translateResult)
    {
        return [
            'src' => $translateResult['src'],
            'dst' => $translateResult['dst'],
            'original' => $translateResult['original'],
        ];
    }
}
