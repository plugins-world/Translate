<?php

namespace Yan\Translate\Contracts;

/**
 * Interface TranslateInterface.
 */
interface TranslateInterface
{
    /**
     * @return string
     */
    public function getSrc(): string;

    /**
     * @return string
     */
    public function getDst(): string;

    /**
     * @return array
     */
    public function getOriginal(): array;
}
