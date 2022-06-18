<?php

namespace MouYong\Translate\Supports;

use ZhenMu\Support\Traits\Arrayable;

class Config implements \ArrayAccess
{
    use Arrayable;

    public function __construct(array $config = [])
    {
        $this->attributes = $config;
    }
}
