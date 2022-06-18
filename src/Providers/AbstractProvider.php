<?php

namespace MouYong\Translate\Providers;

use MouYong\Translate\Contracts\ProviderInterface;
use MouYong\Translate\Supports\Config;

/**
 * Class AbstractProvider.
 */
abstract class AbstractProvider implements ProviderInterface
{
    const HTTP_URL = '';

    const HTTPS_URL = '';
    
    /**
     * Provider name.
     *
     * @var string
     */
    protected $name;

    /**
     * @var Config
     */
    protected $config;

    /**
     * The app id.
     *
     * @var null|string
     */
    protected $appId;

    /**
     * The app key.
     *
     * @var null|string
     */
    protected $appKey;

    /**
     * AbstractProvider constructor.
     *
     * @param null|string $app_id
     * @param null|string $app_key
     * @param array  $config
     */
    public function __construct(?string $app_id, ?string $app_key, array $config = [])
    {
        $this->appId = $app_id;
        $this->appKey = $app_key;

        $this->config = new Config($config);
    }

    /**
     * @return string
     *
     * @throws \ReflectionException
     */
    public function getName()
    {
        if (empty($this->name)) {
            $this->name = strstr((new \ReflectionClass(get_class($this)))->getShortName(), 'Provider', true);
        }

        return $this->name;
    }

    /**
     * Get the translate URL for the provider.
     *
     * @return string
     */
    protected function getTranslateUrl()
    {
        if ($this->config['url']) {
            return $this->config['url'];
        }

        return ($this->config['ssl'] ?? false) 
            ? static::HTTPS_URL
            : static::HTTP_URL;
    }

    abstract public function translate(string $string, $from = 'zh', $to = 'en');

    /**
     * @param array $translateResult
     *
     * @return array
     */
    abstract protected function mapTranslateResult(array $translateResult);
}
