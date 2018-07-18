<?php

namespace Yan\Translate\Contracts;

/**
 * Interface FactoryInterface.
 */
interface FactoryInterface
{
    /**
     * Get an Translate provider implementation.
     *
     * @param string|null $driver
     *
     * @return ProviderInterface
     */
    public function driver($driver = null);
}
