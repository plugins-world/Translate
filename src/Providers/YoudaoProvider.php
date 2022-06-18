<?php

namespace MouYong\Translate\Providers;

use ArrayAccess;
use MouYong\Translate\Contracts\ProviderInterface;
use MouYong\Translate\Exceptions\TranslateException;
use MouYong\Translate\Translate;
use ZhenMu\Support\Traits\Clientable;
use ZhenMu\Support\Traits\DefaultClient;

/**
 * Class YoudaoProvider.
 *
 * @see http://ai.youdao.com/docs/doc-trans-api.s#p02
 */
class YoudaoProvider extends AbstractProvider implements ProviderInterface, ArrayAccess
{
    use Clientable;
    use DefaultClient;

    const HTTP_URL = 'https://openapi.youdao.com/api';

    const HTTPS_URL = 'https://openapi.youdao.com/api';

    protected function getRequestParams($q, $from = 'zh-CHS', $to = 'EN')
    {
        $salt = uniqid();
        $curtime = time();

        $params = [
            'q' => $q,
            'from' => $from,
            'to' => $to,
            'appKey' => $this->appId,
            'salt' => $salt,
            'signType' => $this->config['signType'] ?? 'v3',
            'curtime' => $curtime,
            'ext' => 'mp3',
            'voice' => $this->config['voice'] ?? 0,
            'strict' => $this->config['strict'] ?? 'false',
        ];

        if ($vocabId = $this->config['vocabId']) {
            $params['vocabId'] = $vocabId;
        }

        $params['sign'] = $this->makeSignature($params);

        return $params;
    }

    protected function makeSignature(array $params)
    {
        if ($params['signType'] != 'v3') {
            $signStr = $this->appId.$params['q'].$params['salt'].$this->appKey;

            return md5($signStr);
        }

        return $this->makeV3Signature($params);
    }

    protected function makeV3Signature(array $params)
    {
        $input = $q = $params['q'];

        $qLen = mb_strlen($q);
        if ($qLen > 20) {
            $input = mb_substr($q, 0, 10).$qLen.mb_substr($q, -10);
        }

        $signStr = $this->appId.$input.$params['salt'].$params['curtime'].$this->appKey;

        return hash('sha256', $signStr, false);
    }

    /**
     * {@inheritdoc}
     */
    public function translate($q, $from = 'zh-CHS', $to = 'EN')
    {
        $response = $this->post($this->getTranslateUrl(), [
            'form_params' => $this->getRequestParams($q, $from, $to),
        ]);

        return new Translate($this->mapTranslateResult($response->toArray()));
    }

    protected function mapTranslateResult(array $translateResult)
    {
        return [
            'src' => $translateResult['query'],
            'dst' => head($translateResult['translation']),
            'original' => $translateResult,
        ];
    }

    public function isErrorResponse(array $data): bool
    {
        return '0' != $data['errorCode'];
    }

    public function handleErrorResponse(?string $content = null, array $data = [])
    {
        $errorCode = $data['errorCode'];
        $errorCodeReasonHref = 'https://ai.youdao.com/DOCSIRMA/html/自然语言翻译/API文档/文本翻译服务/文本翻译服务-API文档.html#p02';

        throw new TranslateException(sprintf(
            "请求接口错误，错误码：%s，查看错误原因：%s",
            $errorCode,
            $errorCodeReasonHref
        ), $errorCode);
    }
}
