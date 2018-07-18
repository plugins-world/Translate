<?php

namespace Yan\Translate;

use Yan\Translate\Contracts\TranslateInterface;
use Yan\Translate\Traits\HasAttributes;

class Translate implements TranslateInterface
{
    use HasAttributes;

    /**
     * User constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    public function getSrc(): string
    {
        return $this->getAttribute('src');
    }

    public function getDst(): string
    {
        return $this->getAttribute('dst');
    }

    public function getOriginal(): array
    {
        return $this->getAttribute('original');
    }
}
