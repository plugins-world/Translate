<h1 align="center">Translate</h1>
<p align="center">
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

use Yan\Translate\TranslateManager;

$config = [
    'default' => 'google',

    'drivers' => [
        // 留空
        'google' => [
            'app_id' => '',
            'app_key' => '',
        ],
        
        'baidu' => [
            'ssl' => true,
            'app_id' => 'your-baidu-app_id',
            'app_key' => 'your-baidu-app_key',
        ],

        'youdao' => [
            'ssl' => false,
            'app_id' => '你的有道智云 应用ID',
            'app_key' => '你的有道智云 应用密钥',
        ],

        // 留空
        'jinshan' => [
            'app_id' => '',
            'app_key' => '',
        ]
    ],
];

$socialite = new TranslateManager($config);

$result = $socialite->driver()->translate('测试', 'zh', 'en');
$result = $socialite->driver('google')->translate('测试', 'zh', 'en');
$result = $socialite->driver('baidu')->translate('测试', 'zh', 'en');
$result = $socialite->driver('youdao')->translate('测试', 'zh', 'en');
$result = $socialite->driver('jinshan')->translate('测试', 'zh', 'en');

var_dump($result);
var_dump($result->getSrc());
var_dump($result->getDst());
var_dump($result->getOriginal());
```
