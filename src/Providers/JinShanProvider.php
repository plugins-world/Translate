<?php

namespace Yan\Translate\Providers;

use Yan\Translate\Contracts\ProviderInterface;
use Yan\Translate\Exceptions\TranslateException;
use Yan\Translate\Translate;

class JinShanProvider extends AbstractProvider implements ProviderInterface
{
    const HTTP_URL = 'https://ifanyi.iciba.com/index.php';

    protected function getTranslateUrl()
    {
        return static::HTTP_URL;
    }

    protected function getRequestParams($q, $from, $to)
    {
        return compact('q', 'from', 'to');
    }

    protected function getRequestQuery($q)
    {
        $data         = [
            'c'         => 'trans',
            'm'         => 'fy',
            'client'    => '6',
            'auth_user' => 'key_ciba',
        ];
        $data['sign'] = substr(bin2hex(md5(sprintf("%s%sifanyicjbysdlove1%s", $data['client'],
            $data['auth_user'], $q), true)), 0, 16);

        return http_build_query($data);
    }

    /**
     * {@inheritdoc}
     */
    public function translate($q, $from = 'auto', $to = 'auto')
    {
        $response = $this->post(
            $this->getTranslateUrl().'?'.$this->getRequestQuery($q), $this->getRequestParams($q, $from, $to),
            [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ]);

        $response = json_decode($response, true);

        if (!empty($response['content']['error_code'])) {
            throw new TranslateException($response['content']['message'], $response['content']['error_code']);
        }

        return new Translate($this->mapTranslateResult([
            'src' => $q,
            'dst' => $response['content']['out'],
            'original' => $response,
        ]));
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
