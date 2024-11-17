<?php

namespace Plugins\Translate\Result;

class Translate
{
    protected $attributes = [];
    
    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    public function getSrc()
    {
        return $this->attributes['src'] ?? null;
    }

    public function getDst()
    {
        return $this->attributes['dst'] ?? null;
    }

    public function getOriginal()
    {
        return $this->attributes;
    }
}
