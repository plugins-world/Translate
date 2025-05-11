<?php

namespace Plugins\Translate\Translator;

use Plugins\Translate\Result\Translate;
use Plugins\Translate\Core\Contracts\TranslatorInterface;
use Plugins\Translate\Core\Exceptions\TranslateException;
use Plugins\Translate\Core\Traits\InteractWithConfig;
use Plugins\Translate\Core\Traits\InteractWithHttpClient;
use Plugins\Translate\Utilities\DataUtility;

/**
 * @deprecated v1.0.0
 * @see https://www.iciba.com/fy
 */
class Jinshan implements TranslatorInterface
{
    use InteractWithConfig;
    use InteractWithHttpClient;

    const API_URL = 'https://ifanyi.iciba.com/index.php';

    public function getHttpClientDefaultOptions()
    {
        $http = $this->config['http'] ?? [];

        $options = array_merge(
            [
                'base_uri' => $http['base_uri'] ?? $this->getBaseUri(),
                'timeout' => 5, // 请求 5s 超时
                'http_errors' => false,
                'headers' => [
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
        return Jinshan::API_URL;
    }

    protected function getRequestParams($q, $from, $to)
    {
        return compact('from', 'to', 'q');
    }

    protected function getRequestQuery($q)
    {
        $data         = [
            'c'         => 'trans',
            'm'         => 'fy',
            'client'    => '6',
            'auth_user' => 'key_web_new_fanyi',
        ];

        // @see https://github.com/liuyug/code_example/blob/4be273f1e4aad1a1c6ded72d64997ea13c165df6/iciba.py#L16
        $signKey = '40f9cc60ddc4c76d';

        $data['sign'] = substr(bin2hex(md5(sprintf(
            "%s%s%s%s",
            $data['client'],
            $data['auth_user'],
            $signKey,
            $q
        ), true)), 0, 16);

        return $data;
    }

    public function translate(string $q, $source_lang = 'auto', $target_lang = 'en'): mixed
    {
        DataUtility::ensureLangTagSupport($source_lang, $target_lang, 'jinshan');

        $response = $this->getHttpClient()->request('POST', '/', [
            'query' => $this->getRequestQuery($q),
            'form_params' => $this->getRequestParams($q, $source_lang, $target_lang),
        ]);

        $result = json_decode($response->getBody()->getContents(), true) ?? [];

        if (empty($result)) {
            throw new TranslateException("请求接口错误，未获取到翻译结果");
        }

        if (!empty($result['error_code'])) {
            throw new TranslateException("请求接口错误，错误信息：{$source_lang} => {$target_lang}, {$result['message']}", $result['error_code']);
        }

        return new Translate($this->mapTranslateResult([
            'src' => $q,
            'dst' => $result['content']['out'] ?? null,
            'original' => $result,
        ]));
    }

    public function mapTranslateResult(array $translateResult): array
    {
        return [
            'src' => $translateResult['src'],
            'dst' => $translateResult['dst'],
            'original' => $translateResult['original'],
        ];
    }
}
