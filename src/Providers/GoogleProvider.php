<?php

namespace MouYong\Translate\Providers;

use MouYong\Translate\Translate;
use MouYong\Translate\Contracts\ProviderInterface;
use MouYong\Translate\Clients\GoogleTranslateClient;
use MouYong\Translate\Exceptions\TranslateException;

class GoogleProvider extends AbstractProvider implements ProviderInterface
{
    protected $translateClient;

    public function translate(string $q, $from = 'zh-CN', $to = 'en')
    {
        $translateClient = $this->getTranslateClient($from, $to);

        try {
            $response = $translateClient->translate($q);
        } catch (\Throwable $e) {
            throw new TranslateException("请求接口错误，错误信息：{$e->getMessage()}", $e->getCode());
        }

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
     * @return \Stichoza\GoogleTranslate\GoogleTranslate
     *
     * @throws \Exception
     */
    public function getTranslateClient($from, $to)
    {
        $translateClient = new GoogleTranslateClient($this->config);

        return $translateClient
            ->setSource($from)
            ->setTarget($to);
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
