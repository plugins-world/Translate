<?php

namespace MouYong\Translate\Supports;

class Config implements \ArrayAccess
{
    use \MouYong\Translate\Traits\Arrayable;
    
    protected $attributes = [];
    
    public function __construct(array $config = [])
    {
        $this->attributes = $config;
    }
}
