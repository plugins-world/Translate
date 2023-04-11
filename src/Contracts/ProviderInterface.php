<?php

namespace MouYong\Translate\Contracts;

/**
 * Interface ProviderInterface.
 */
interface ProviderInterface
{
    /**
     * Translate giving string from.
     *
     * @param string $string
     * @param string $from
     * @param string $to
     *
     * @return mixed
     */
    public function translate(string $string, $from = 'zh', $to = 'en');
}
