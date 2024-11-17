<?php

namespace Plugins\Translate\Translator;

use Plugins\Translate\Result\Translate;
use Plugins\Translate\Core\Contracts\TranslatorInterface;
use Plugins\Translate\Core\Exceptions\TranslateException;
use Plugins\Translate\Core\Traits\InteractWithConfig;
use Plugins\Translate\Core\Traits\InteractWithHttpClient;
use Plugins\Translate\Utilities\DataUtility;

/**
 * @see http://api.fanyi.baidu.com/manage/developer
 * 
 * @see http://api.fanyi.baidu.com/api/trans/product/apidoc
 */
class Baidu implements TranslatorInterface
{
    use InteractWithConfig;
    use InteractWithHttpClient;

    const HTTP_API_URL = 'http://api.fanyi.baidu.com/api/trans/vip/translate';

    const HTTPS_API_URL = 'https://fanyi-api.baidu.com/api/trans/vip/translate';

    public function getHttpClientDefaultOptions()
    {
        $http = $this->config['http'] ?? [];

        $options = array_merge(
            [
                'base_uri' => $http['base_uri'] ?? $this->getBaseUri(),
                'timeout' => 5, // 请求 5s 超时
                'http_errors' => false,
                'headers' => [
                    'Authorization' => "DeepL-Auth-Key {$this->getAppKey()}",
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
            ],
            $http
        );

        return $options;
    }

    public function getBaseUri()
    {
        $base_uri = Baidu::HTTP_API_URL;
        if ($this->config['is_use_https_api'] ?? true) {
            $base_uri = Baidu::HTTPS_API_URL;
        }

        return $base_uri;
    }

    public function getAppId()
    {
        return $this->config['app_id'] ?? null;
    }

    public function getAppKey()
    {
        return $this->config['app_key'] ?? null;
    }

    protected function getRequestParams($q, $from = 'auto', $to = 'en')
    {
        $salt = time();

        $params = [
            'q' => $q,
            'from' => $from ?: 'auto',
            'to' => $to ?: 'en',
            'appid' => $this->getAppId(),
            'salt' => $salt,
            'tts' => $this->config['tts'] ?? 1,
            'dict' => $this->config['dict'] ?? 1,
            'action' => $this->config['action'] ?? 0,
        ];


        $params['sign'] = $this->makeSignature($params);

        return $params;
    }

    protected function makeSignature(array $params)
    {
        return md5($this->getAppId().$params['q'].$params['salt'].$this->getAppKey());
    }

    /**
     * @param  string $q
     * @param  string $from
     * @param  string $to
     * 
     * @return Translate
     * 
     * @see https://fanyi-api.baidu.com/api/trans/vip/translate
     */
    public function translate(string $q, $source_lang = 'zh', $target_lang = 'en'): mixed
    {
        DataUtility::ensureLangTagSupport($source_lang, $target_lang, 'baidu');

        $response = $this->getHttpClient()->request('POST', '', [
            'form_params' => $this->getRequestParams($q, $source_lang, $target_lang),
        ]);

        $result = json_decode($response->getBody()->getContents(), true) ?? [];

        if (empty($result)) {
            throw new TranslateException("请求接口错误，未获取到翻译结果");
        }

        if (!empty($result['error_code'])) {
            $message = $result['error_msg'];
            if ($result['error_code'] === '58001') {
                $message = '小语种暂不支持';
            }

            throw new TranslateException("请求接口错误，错误信息：{$source_lang} => {$target_lang}, error_code: {$result['error_code']}, error_msg: {$message}");
        }

        return new Translate($this->mapTranslateResult($result));
    }

    public function mapTranslateResult(array $translateResult): array
    {
        return [
            'src' => reset($translateResult['trans_result'])['src'],
            'dst' => reset($translateResult['trans_result'])['dst'],
            'original' => $translateResult,
        ];
    }
}
