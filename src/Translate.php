<?php

namespace MouYong\Translate;

use ZhenMu\Support\Traits\Arrayable;

class Translate
{
    use Arrayable;

    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    public function getSrc()
    {
        return $this['src'];
    }

    public function getDst()
    {
        return $this['dst'];
    }

    public function getOriginal()
    {
        return $this->toArray();
    }
}
