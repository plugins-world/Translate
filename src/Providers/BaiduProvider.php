<?php

namespace MouYong\Translate\Providers;

use ArrayAccess;
use MouYong\Translate\Translate;
use ZhenMu\Support\Traits\Clientable;
use ZhenMu\Support\Traits\DefaultClient;
use MouYong\Translate\Contracts\ProviderInterface;
use MouYong\Translate\Exceptions\TranslateException;

/**
 * Class BaiduProvider.
 *
 * @see http://api.fanyi.baidu.com/api/trans/product/apidoc
 */
class BaiduProvider extends AbstractProvider implements ProviderInterface
{
    use Clientable;

    const HTTP_URL = 'http://api.fanyi.baidu.com/api/trans/vip/translate';

    const HTTPS_URL = 'https://fanyi-api.baidu.com/api/trans/vip/translate';

    protected function getRequestParams($q, $from = 'zh', $to = 'en')
    {
        $salt = time();

        $params = [
            'q' => $q,
            'from' => $from ?: 'zh',
            'to' => $to ?: 'en',
            'appid' => $this->appId,
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
        return md5($this->appId.$params['q'].$params['salt'].$this->appKey);
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
    public function translate(string $q, $from = 'zh', $to = 'en')
    {
        $response = $this->post($this->getTranslateUrl(), [
            'form_params' => $this->getRequestParams($q, $from, $to),
        ]);

        return new Translate($this->mapTranslateResult($response));
    }

    protected function mapTranslateResult(array $translateResult)
    {
        return [
            'src' => reset($translateResult['trans_result'])['src'],
            'dst' => reset($translateResult['trans_result'])['dst'],
            'original' => $translateResult,
        ];
    }

    public function isErrorResponse(array $data): bool
    {
        return !empty($data['error_code']);
    }

    public function handleErrorResponse(?string $content = null, array $data = [])
    {
        throw new TranslateException("请求接口错误，错误信息：{$data['error_msg']}", $data['content']['error_code']);
    }
}
