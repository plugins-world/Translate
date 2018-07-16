<?php

namespace Yan\Translate\Drivers;

class Baidu extends AbstractTranslate
{
    protected $httpUrl = 'http://api.fanyi.baidu.com/api/trans/vip/translate';

    protected $httpsUrl = 'https://fanyi-api.baidu.com/api/trans/vip/translate';

    /**
     * @param string $content
     * @param int    $salt
     *
     * @return string
     *
     * @see http://api.fanyi.baidu.com/api/trans/product/apidoc 百度
     * @see http://ai.youdao.com/docs/doc-trans-api.s#p02 有道
     */
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
