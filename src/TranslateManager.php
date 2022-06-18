<?php

namespace MouYong\Translate;

use Closure;
use InvalidArgumentException;
use MouYong\Translate\Supports\Config;
use MouYong\Translate\Contracts\ProviderInterface;

class TranslateManager
{
    /**
     * The configuration.
     *
     * @var Config
     */
    protected $config;

    /**
     * The registered custom driver creators.
     *
     * @var array
     */
    protected $customCreators = [];

    /**
     * The initial drivers.
     *
     * @var array
     */
    protected $initialDrivers = [
        'baidu' => 'Baidu',
        'youdao' => 'Youdao',
        'google' => 'Google',
        'jinshan' => 'JinShan',
    ];

    protected $defaultDriver;

    /**
     * The array of created "drivers".
     *
     * @var ProviderInterface[]
     */
    protected $drivers = [];

    /**
     * TranslateManager constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = new Config($config);

        if (!empty($config['default'])) {
            $this->setDefaultDriver($config['default']);
        }
    }

    /**
     * Set config instance.
     *
     * @param Config $config
     *
     * @return $this
     */
    public function config(Config $config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Get a driver instance.
     *
     * @param string|null $driver
     *
     * @return ProviderInterface
     */
    public function driver($driver = null)
    {
        $driver = $driver ?: $this->getDefaultDriver();

        if (!isset($this->drivers[$driver])) {
            $this->drivers[$driver] = $this->createDriver($driver);
        }

        return $this->drivers[$driver];
    }

    public function getDefaultDriver()
    {
        if (empty($this->defaultDriver)) {
            throw new \RuntimeException();
        }

        return $this->defaultDriver;
    }

    public function setDefaultDriver($driver)
    {
        $this->defaultDriver = $driver;

        return $this;
    }

    /**
     * Create a new driver instance.
     *
     * @param string $driver
     *
     * @throws \InvalidArgumentException
     *
     * @return ProviderInterface
     */
    protected function createDriver($driver)
    {
        if (isset($this->initialDrivers[$driver])) {
            $provider = $this->initialDrivers[$driver];
            $provider = __NAMESPACE__.'\\Providers\\'.$provider.'Provider';

            return $this->buildProvider($provider, $this->formatConfig(
                $this->config["drivers.{$driver}"]
            ));
        }

        if (isset($this->customCreators[$driver])) {
            return $this->callCustomCreator($driver);
        }

        throw new InvalidArgumentException("Driver [$driver] not supported.");
    }

    /**
     * Call a custom driver creator.
     *
     * @param string $driver
     *
     * @return ProviderInterface
     */
    protected function callCustomCreator($driver)
    {
        return $this->customCreators[$driver]($this->config);
    }

    /**
     * Register a custom driver creator Closure.
     *
     * @param string   $driver
     * @param \Closure $callback
     *
     * @return $this
     */
    public function extend($driver, Closure $callback)
    {
        $this->customCreators[$driver] = $callback;

        return $this;
    }

    /**
     * Get all of the created "drivers".
     *
     * @return ProviderInterface[]
     */
    public function getDrivers()
    {
        return $this->drivers;
    }

    /**
     * Build an Translate provider instance.
     *
     * @param string $provider
     * @param array  $config
     *
     * @return ProviderInterface
     */
    public function buildProvider($provider, array $config = [])
    {
        return new $provider(
            $config['app_id'] ?? null,
            $config['app_key'] ?? null,
            $config,
        );
    }

    /**
     * Format the server configuration.
     *
     * @param Config $config
     *
     * @return array
     */
    public function formatConfig(\ArrayAccess $config)
    {
        return array_merge([
            'http' => $this->config['http'],
            'app_id' => $config['app_id'],
            'app_key' => $config['app_key'],
        ], $config->toArray());
    }
}
