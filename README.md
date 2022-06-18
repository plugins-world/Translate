<h1 style="text-align: center;">Translate</h1>
<p style="text-align: center;">
<a href="https://travis-ci.org/mouyong/translate"><img src="https://travis-ci.org/mouyong/translate.svg?branch=master" alt="Build Status"></a>
</p>

# Requirement

```
PHP >= 5.5
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

$translate = new TranslateManager($config);

$result = $translate->driver()->translate('测试', 'zh', 'en');
$result = $translate->driver('google')->translate('测试', 'zh', 'en');
$result = $translate->driver('baidu')->translate('测试', 'zh', 'en');
$result = $translate->driver('youdao')->translate('测试', 'zh', 'en');
$result = $translate->driver('jinshan')->translate('测试', 'zh', 'en');

var_dump($result);
var_dump($result->getSrc());
var_dump($result->getDst());
var_dump($result->getOriginal());
```
