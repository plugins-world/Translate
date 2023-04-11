<?php

namespace MouYong\Translate;

class Translate
{
    use \MouYong\Translate\Traits\Arrayable;

    protected $attributes = [];
    
    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    public function getSrc()
    {
        return $this->get($this->attributes, 'src');
    }

    public function getDst()
    {
        return $this->get($this->attributes, 'dst');
    }

    public function getOriginal()
    {
        return $this->attributes;
    }
}
