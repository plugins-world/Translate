多平台翻译、文本语言探测
---

[![Latest Stable Version](http://poser.pugx.org/plugins-world/translate/v)](https://packagist.org/packages/plugins-world/translate) [![Total Downloads](http://poser.pugx.org/plugins-world/translate/downloads)](https://packagist.org/packages/plugins-world/translate) [![Latest Unstable Version](http://poser.pugx.org/plugins-world/translate/v/unstable)](https://packagist.org/packages/plugins-world/translate) [![License](http://poser.pugx.org/plugins-world/translate/license)](https://packagist.org/packages/plugins-world/translate) [![PHP Version Require](http://poser.pugx.org/plugins-world/translate/require/php)](https://packagist.org/packages/plugins-world/translate)


项目自动拆分，如需跟踪源码更新情况，请前往：https://github.com/plugins-world/packages 查看 Translate 目录

# 安装

```shell
$ composer require "plugins-world/translate" -vvv
```

# 使用


```php
<?php

require __DIR__ . '/vendor/autoload.php';

// deepl
// $app = new \Plugins\Translate\Translator\Deepl([
//     // 需要配置代理
//     'is_use_pro_api' => false,
//     'pro_app_key' => '',
//     'free_app_key' => '',
//     'is_enable_proxy' => true,
//     'http_proxy' => 'http://10.0.50.101:7890',
//     'http_proxys' => 'http://10.0.50.101:7890',
// ]);

// google
// $app = new \Plugins\Translate\Translator\Google\Google([
//     // 需要配置代理
//     'is_enable_proxy' => true,
//     'http_proxy' => 'http://10.0.50.101:7890',
//     'https_proxy' => 'http://10.0.50.101:7890',
// ]);

// baidu
// $app = new \Plugins\Translate\Translator\Baidu([
//     // @see http://api.fanyi.baidu.com/manage/developer
//     // 'app_id' => '你的百度翻译 app_id',
//     // 'app_key' => '你的百度翻译 app_key',
// ]);

// youdao
// $app = new \Plugins\Translate\Translator\Youdao([
//     // @see https://ai.youdao.com/console/
//     // 'app_id' => '你的有道智云 app_id',
//     // 'app_key' => '你的有道智云 app_key',
// ]);

// jinshan
// 金山翻译无法使用，验证时间 2024-04-29 21:56:00
// $app = new \Plugins\Translate\Translator\Jinshan();


// try {
//     $result = $app->translate('测试', 'zh', 'en');
//     var_dump($result->getSrc(), $result->getDst(), $result->getOriginal());
// } catch (\Throwable $e) {
//     var_dump($e->getMessage());
// }
// die;


// 文本内容探测：检测用户输入的内容是哪个国家的语言
$languageRecognizerClient = new \Plugins\Translate\LanguageRecognizer\LanguageRecognizerClient();

$languageRecognizer = $languageRecognizerClient->detect("Словѣ́ньскъ/ⰔⰎⰑⰂⰡⰐⰠⰔⰍⰟ");
var_dump($languageRecognizer->getData());

```

## TODO

[x] Deepl  
[ ] Bing  
[ ] Tencent  
