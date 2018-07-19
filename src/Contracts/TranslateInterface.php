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
    public function getSrc();

    /**
     * @return string
     */
    public function getDst();

    /**
     * @return array
     */
    public function getOriginal();
}
