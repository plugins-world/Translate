<?php

namespace Yan\Translate\Contracts;

interface TranslateInterface
{
    public function translate(string $content, $from = null, $to = null);
}
