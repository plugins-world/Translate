<?php

namespace Plugins\Translate\Utilities;

use Plugins\Translate\Core\Exceptions\TranslateException;

class DataUtility
{
    public static function getJsonDataFromFile($filepath)
    {
        $filename = basename($filepath);
        $realfilepath = realpath($filepath);
        if (!$realfilepath) {
            throw new \RuntimeException("{$filename} 的 json 数据不存在, 路径为：{$filepath}");
        }

        $content = file_get_contents($realfilepath);
        $data = json_decode($content, true) ?? [];

        return $data;
    }

    public static function getLanguageList(string $type)
    {
        $filename = match ($type) {
            'baidu' => 'lang_list_baidu.json',
            'youdao' => 'lang_list_youdao.json',
            'google' => 'lang_list_google.json',
            'deepl' => 'lang_list_deepl.json',
        };

        return DataUtility::getJsonDataFromFile(__DIR__."/../Dictionaries/{$filename}");
    }

    public static function ensureSupportLang($lang, $type)
    {
        $languageList = DataUtility::getLanguageList($type);

        foreach (array_keys($languageList) as $langTag) {
            $lowerLangTag = strtolower($langTag);

            if (str_contains($lowerLangTag, $lang)) {
                return true;
            }
        }

        return false;
    }

    public static function ensureLangTagSupport($source_lang, $target_lang, $type)
    {
        if (!DataUtility::ensureSupportLang($source_lang, $type)) {
            throw new TranslateException("请求接口错误，不支持的 source_lang {$source_lang}");
        }

        if (!DataUtility::ensureSupportLang($target_lang, $type)) {
            throw new TranslateException("请求接口错误，不支持的 target_lang {$target_lang}");
        }
    }

    public static function getLanguageCodes()
    {
        $langTagList = DataUtility::getJsonDataFromFile(__DIR__."/../Dictionaries/language_codes.json");

        $data = [];
        foreach ($langTagList as $item) {
            $codeInfo = explode('-', $item['code']);

            $item['codeCountry'] = $codeInfo[0];
            $item['codeCity'] = $codeInfo[1] ?? null;

            $data[] = $item;
        }

        return $data;
    }
}
