<?php

namespace Plugins\Translate\Core\Traits;

trait InteractWithConfig
{
    protected $config = [];

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }
}
