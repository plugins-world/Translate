<?php

namespace Yan\Translate\Providers;

use Yan\Translate\Contracts\ProviderInterface;
use Yan\Translate\Exceptions\TranslateException;
use Yan\Translate\Translate;

class BaiduProvider extends AbstractProvider implements ProviderInterface
{
    const HTTP_URL = 'http://api.fanyi.baidu.com/api/trans/vip/translate';

    const HTTPS_URL = 'https://fanyi-api.baidu.com/api/trans/vip/translate';

    protected function getRequestParams(array $args): array
    {
        list($q, $from, $to) = $args;

        $salt = time();

        $params = [
            'from' => $from ?? 'zh',
            'to' => $to ?? 'en',
            'appid' => $this->appId,
            'q' => $q,
            'salt' => $salt,
        ];

        $params['sign'] = $this->makeSignature($params);

        return $params;
    }

    protected function makeSignature(array $params): string
    {
        return md5($this->appId.$params['q'].$params['salt'].$this->appKey);
    }

    /**
     * {@inheritdoc}
     */
    public function translate(string $q, $from = 'zh', $to = 'en')
    {
        $response = $this->post($this->getTranslateUrl(), $this->getRequestParams(func_get_args()));

        if (!empty($response['error_code'])) {
            throw new TranslateException($response['error_msg'], $response['error_code']);
        }

        return new Translate($this->mapTranslateResult($response));
    }

    protected function mapTranslateResult(array $translateResult): array
    {
        return [
            'src' => reset($translateResult['trans_result'])['src'],
            'dst' => reset($translateResult['trans_result'])['dst'],
            'original' => $translateResult,
        ];
    }
}
