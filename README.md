Translate content and recognizer content language
---

[![Build Status](https://api.travis-ci.com/mouyong/translate.svg?branch=master)](https://travis-ci.com/mouyong/translate) [![Latest Stable Version](http://poser.pugx.org/mouyong/translate/v)](https://packagist.org/packages/mouyong/translate) [![Total Downloads](http://poser.pugx.org/mouyong/translate/downloads)](https://packagist.org/packages/mouyong/translate) [![Latest Unstable Version](http://poser.pugx.org/mouyong/translate/v/unstable)](https://packagist.org/packages/mouyong/translate) [![License](http://poser.pugx.org/mouyong/translate/license)](https://packagist.org/packages/mouyong/translate) [![PHP Version Require](http://poser.pugx.org/mouyong/translate/require/php)](https://packagist.org/packages/mouyong/translate)


# Requirement

```
PHP >= 7.1
```

# Installation

```shell
$ composer require "mouyong/translate" -vvv
```

# Usage


```php
<?php

use MouYong\Translate\TranslateManager;

$config = [
    'default' => 'jinshan',

    'http' => [],

    'drivers' => [
        // 留空
        'jinshan' => [
            'ssl' => true,
            'app_id' => '',
            'app_key' => '',
        ],

        // @see http://api.fanyi.baidu.com/api/trans/product/desktop
        'baidu' => [
            'ssl' => false,
            'app_id' => '你的百度翻译 app_id',
            'app_key' => '你的百度翻译 app_key',
        ],

        // @see https://ai.youdao.com/console/
        'youdao' => [
            'ssl' => false,
            'app_id' => '你的有道智云 app_id',
            'app_key' => '你的有道智云 app_key',
        ],

        // 留空, 需要配置 http 代理
        // @see https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html#proxy-option
        'google' => [
            'app_id' => '',
            'app_key' => '',
        ],
    ],
];


// 翻译
$translate = new \MouYong\Translate\TranslateManager($config);

$result = $translate->driver()->translate('测试', 'zh', 'en');
$result = $translate->driver('baidu')->translate('测试', 'zh', 'en');
$result = $translate->driver('jinshan')->translate('测试', 'zh', 'en');
$result = $translate->driver('youdao')->translate('测试', 'zh', 'en');
$result = $translate->driver('google')->translate('测试', 'zh', 'en');

var_dump($result);


// 文本内容探测：检测用户输入的内容是哪个国家的语言
$languageRecognizerClient = new \MouYong\Translate\Clients\LanguageRecognizerClient();

$languageRecognizer = $languageRecognizerClient->detect("Словѣ́ньскъ/ⰔⰎⰑⰂⰡⰐⰠⰔⰍⰟ");
var_dump($languageRecognizer->getData());
```
