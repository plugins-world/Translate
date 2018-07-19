<?php

namespace Yan\Translate\Providers;

use Yan\Translate\Contracts\ProviderInterface;
use Yan\Translate\Exceptions\TranslateException;
use Yan\Translate\Translate;

class JinShanProvider extends AbstractProvider implements ProviderInterface
{
    const HTTP_URL = 'http://fy.iciba.com/ajax.php?a=fy';

    protected function getTranslateUrl()
    {
        return static::HTTP_URL;
    }

    protected function getRequestParams($w, $f, $t)
    {
        return compact('w', 'f', 't');
    }

    /**
     * {@inheritdoc}
     */
    public function translate($q, $from = 'auto', $to = 'auto')
    {
        $response = $this->post($this->getTranslateUrl(), $this->getRequestParams($q, $from, $to));

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
