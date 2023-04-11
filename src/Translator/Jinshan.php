<?php

namespace MouYong\Translate\Translator;

use MouYong\Translate\Translator\Result\Translate;
use MouYong\Translate\Kernel\Contracts\TranslatorInterface;
use MouYong\Translate\Kernel\Exceptions\TranslateException;

/**
 * @see https://www.iciba.com/fy
 */
class Jinshan implements TranslatorInterface
{
    use \MouYong\Translate\Kernel\Traits\InteractWithConfig;
    use \MouYong\Translate\Kernel\Traits\InteractWithHttpClient;

    public function getHttpClientDefaultOptions()
    {
        return array_merge(
            [
                'base_uri' => 'https://ifanyi.iciba.com/index.php'
            ],
            (array) ($this->config['http'] ?? []),
        );
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
            'auth_user' => 'key_web_fanyi',
        ];

        // @see https://github.com/liuyug/code_example/blob/4be273f1e4aad1a1c6ded72d64997ea13c165df6/iciba.py#L16
        $signKey = 'ifanyiweb8hc9s98e';

        $data['sign'] = substr(bin2hex(md5(sprintf(
            "%s%s%s%s",
            $data['client'],
            $data['auth_user'],
            $signKey,
            $q
        ), true)), 0, 16);

        return $data;
    }

    public function translate(string $q, $from = 'auto', $to = 'auto'): mixed
    {
        $response = $this->getHttpClient()->request('POST', '/', [
            'query' => $this->getRequestQuery($q),
            'body' => $this->getRequestParams($q, $from, $to),
        ]);

        $result = $response->toArray();

        if (!empty($result['error_code'])) {
            throw new TranslateException("请求接口错误，错误信息：{$result['message']}", $result['error_code']);
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
