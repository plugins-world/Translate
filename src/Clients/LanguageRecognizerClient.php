<?php

namespace MouYong\Translate\Clients;

use ZhenMu\Support\Traits\Clientable;
use MouYong\Translate\LanguageRecognizer;
use MouYong\Translate\Exceptions\LanguageDetectException;

/**
 * @class LanguageRecognizer
 * 
 * @see https://translatedlabs.com/语言识别器
 * 
 * @date 2022-06-18
 */
class LanguageRecognizerClient
{
    use Clientable;

    const API_URL = 'https://api.translatedlabs.com/language-identifier/identify';

    public function detect(?string $content)
    {
        if (!$content) {
            return null;
        }

        $body = json_decode(sprintf('{
            "etnologue": true,
            "uiLanguage": "zh",
            "text": "%s"
        }', $content), true);


        $response = $this->post(static::API_URL, [
            'json' => $body,
        ]);

        return new LanguageRecognizer([
            'detectContent' => $content,
        ] + $response);
    }

    public function isErrorResponse(array $data): bool
    {
        return !empty($data['error']);
    }

    public function handleErrorResponse(?string $content = null, array $data = [])
    {
        throw new LanguageDetectException("请求接口错误，错误信息：{$data['error']}");
    }
}
