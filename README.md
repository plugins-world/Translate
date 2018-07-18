# Requirement

```
PHP >= 7.0
```

# Installation

```shell
$ composer require "yan/translate" -vvv
```

# Usage


```php
<?php

use Yan\Translate\TranslateManager;

$config = [
    'default' => 'google',

    'drivers' => [
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
        'google' => [
            'app_id' => '',
            'app_key' => '',
        ],

        // 留空
        'jinshan' => [
            'app_id' => '',
            'app_key' => '',
        ]
    ],
];

$socialite = new TranslateManager($config);

$result = $socialite->driver('baidu')->translate('测试', 'zh', 'en');

var_dump($result);
```
