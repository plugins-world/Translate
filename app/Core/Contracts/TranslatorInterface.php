<?php

namespace Plugins\Translate\Core\Contracts;

/**
 * Interface ProviderInterface.
 */
interface TranslatorInterface
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
    public function translate(string $string, $from = 'zh', $to = 'en'): mixed;

    /**
     * @param array $translateResult
     *
     * @return array
     */
    public function mapTranslateResult(array $translateResult): array;
}
