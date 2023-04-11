<?php

namespace MouYong\Translate\Translator;

use MouYong\Translate\Translator\Result\Translate;
use MouYong\Translate\Kernel\Contracts\TranslatorInterface;
use MouYong\Translate\Kernel\Exceptions\TranslateException;

/**
 * @see http://ai.youdao.com/docs/doc-trans-api.s#p02
 */
class Youdao implements TranslatorInterface
{
    use \MouYong\Translate\Kernel\Traits\InteractWithConfig;
    use \MouYong\Translate\Kernel\Traits\InteractWithHttpClient;

    public function getHttpClientDefaultOptions()
    {
        return array_merge(
            [
                'base_uri' => 'https://openapi.youdao.com/api',
            ],
            (array) ($this->config['http'] ?? []),
        );
    }

    public function getAppId()
    {
        return $this->config['app_id'] ?? null;
    }

    public function getAppKey()
    {
        return $this->config['app_key'] ?? null;
    }

    protected function getRequestParams($q, $from = 'zh-CHS', $to = 'EN')
    {
        $salt = uniqid();
        $curtime = time();

        $params = [
            'q' => $q,
            'from' => $from,
            'to' => $to,
            'appKey' => $this->getAppId(),
            'salt' => $salt,
            'signType' => $this->config['signType'] ?? 'v3',
            'curtime' => $curtime,
            'ext' => 'mp3',
            'voice' => $this->config['voice'] ?? 0,
            'strict' => $this->config['strict'] ?? 'false',
        ];

        if ($vocabId = $this->config['vocabId'] ?? null) {
            $params['vocabId'] = $vocabId;
        }

        $params['sign'] = $this->makeSignature($params);

        return $params;
    }

    protected function makeSignature(array $params)
    {
        if ($params['signType'] != 'v3') {
            $signStr = $this->getAppId().$params['q'].$params['salt'].$this->getAppKey();

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

        $signStr = $this->getAppId().$input.$params['salt'].$params['curtime'].$this->getAppKey();

        return hash('sha256', $signStr, false);
    }

    /**
     * {@inheritdoc}
     */
    public function translate($q, $from = 'zh-CHS', $to = 'EN'): mixed
    {
        $response = $this->getHttpClient()->request('POST', '', [
            'body' => $this->getRequestParams($q, $from, $to),
        ]);

        $result = $response->toArray();

        if ($this->isErrorResponse($result)) {
            $this->handleErrorResponse($result);
        }

        return new Translate($this->mapTranslateResult($result));
    }

    public function mapTranslateResult(array $translateResult): array
    {
        return [
            'src' => $translateResult['query'],
            'dst' => current($translateResult['translation']),
            'original' => $translateResult,
        ];
    }

    public function isErrorResponse(array $data): bool
    {
        return '0' != $data['errorCode'];
    }

    public function handleErrorResponse(array $data = [])
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
