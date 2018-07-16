<?php

namespace Yan\Translate\Drivers;

class Youdao extends AbstractTranslate
{
    protected $httpUrl = 'http://openapi.youdao.com/api';

    protected $httpsUrl = 'https://openapi.youdao.com/api';

    protected function getQuery()
    {
        $salt = time();

        $query = [
            'q' => $this->content,
            'from' => $this->form,
            'to' => $this->to,
            'appKey' => $this->config->get('app_id'),
            'salt' => $salt,
            'sign' => $this->generateSign($this->content, $salt),
            'ext' => 'mp3',
            'voice' => 0,
        ];

        return $query;
    }

    protected function generateSign(string $content, int $salt)
    {
        $signString = sprintf('%s%s%s%s',
            $this->config->get('app_id'),
            $content,
            $salt,
            $this->config->get('app_key')
        );

        return md5($signString);
    }
}
