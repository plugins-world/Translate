<?php

namespace Plugins\Translate\LanguageRecognizer;

use Plugins\Translate\Core\Exceptions\LanguageDetectException;
use Plugins\Translate\Core\Traits\InteractWithHttpClient;

/**
 * @class LanguageRecognizer
 * 
 * @see https://translatedlabs.com/语言识别器
 * 
 * @date 2022-06-18
 */
class LanguageRecognizerClient
{
    use InteractWithHttpClient;

    const API_URL = 'https://api.translatedlabs.com/language-identifier/identify';

    public function detect(?string $content)
    {
        if (!$content) {
            return null;
        }

        // todo: 因改变 json 请求方式，不再使用 json_decode(sprintf(), true) 处理参数。下方两行修复代码可能并不需要了
        // $fixedContent = str_replace("\u{A0}", ' ', $content); // 修复非断空格字符导致的不能检测问题
        // $fixedContent = str_replace(["\r", "\n"], ' ', $content); // 修复含有换行符导致内容不能检测的问题
        // $content = $fixedContent;

        $response = $this->getHttpClient()->request('POST', static::API_URL, [
            'json' => [
                'etnologue' => true,
                'uiLanguage' => true,
                'text' => $content,
            ],
        ]);

        $result = json_decode($response->getBody()->getContents(), true) ?? [];

        if ($this->isErrorResponse($result)) {
            $this->handleErrorResponse($result);   
        }

        return new LanguageRecognizer([
            'detectContent' => $content,
        ] + $result);
    }

    public function isErrorResponse(array $data): bool
    {
        return !empty($data['error']);
    }

    public function handleErrorResponse(array $data = [])
    {
        throw new LanguageDetectException("请求接口错误，错误信息：{$data['error']}");
    }
}
