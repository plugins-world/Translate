<?php

namespace Plugins\Translate\Translator;

use Plugins\Translate\Result\Translate;
use Plugins\Translate\Core\Clients\GoogleTranslateClient;
use Plugins\Translate\Core\Contracts\TranslatorInterface;
use Plugins\Translate\Core\Exceptions\TranslateException;
use Plugins\Translate\Core\Traits\InteractWithConfig;
use Plugins\Translate\Utilities\DataUtility;

class Google implements TranslatorInterface
{
    use InteractWithConfig;

    /**
     * @param string $from
     * @param string $to
     *
     * @return \Stichoza\GoogleTranslate\GoogleTranslate
     *
     * @throws \Exception
     */
    public function getTranslateClient($from, $to)
    {
        $translateClient = new GoogleTranslateClient($this->config);

        return $translateClient
            ->resetOptions()
            ->setSource($from)
            ->setTarget($to);
    }

    public function translate(string $q, $source_lang = 'zh-CN', $target_lang = 'en'): mixed
    {
        DataUtility::ensureLangTagSupport($source_lang, $target_lang, 'google');

        $translateClient = $this->getTranslateClient($source_lang, $target_lang);

        try {
            $result = $translateClient->translate($q);
        } catch (\Throwable $e) {
            throw new TranslateException("请求接口错误，错误信息：{$source_lang} => {$target_lang}, {$e->getMessage()}", $e->getCode());
        }

        $rawResponse = $translateClient->getResponse($q);

        return new Translate($this->mapTranslateResult([
            'src' => $q,
            'dst' => $result,
            'original' => $rawResponse,
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
