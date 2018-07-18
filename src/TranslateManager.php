<?php

namespace Yan\Translate;

use Closure;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Yan\Translate\Contracts\FactoryInterface;
use Yan\Translate\Contracts\ProviderInterface;
use Yan\Translate\Supports\Config;

class TranslateManager implements FactoryInterface
{
    /**
     * The configuration.
     *
     * @var Config
     */
    protected $config;

    /**
     * The request instance.
     *
     * @var Request
     */
    protected $request;

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
     * @param array        $config
     * @param Request|null $request
     */
    public function __construct(array $config, Request $request = null)
    {
        $this->config = new Config($config);

        if (!empty($config['default'])) {
            $this->setDefaultDriver($config['default']);
        }

        if ($request) {
            $this->setRequest($request);
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
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return $this
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->request ?: $this->createDefaultRequest();
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

            return $this->buildProvider($provider, $this->formatConfig($this->config->get("drivers.{$driver}")));
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
     * Create default request instance.
     *
     * @return Request
     */
    protected function createDefaultRequest()
    {
        $request = Request::createFromGlobals();

        return $request;
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
    public function buildProvider($provider, $config)
    {
        return new $provider(
            $this->getRequest(),
            $config['app_id'],
            $config['app_key'],
            $config
        );
    }

    /**
     * Format the server configuration.
     *
     * @param array $config
     *
     * @return array
     */
    public function formatConfig(array $config)
    {
        return array_merge([
            'app_id' => $config['app_id'],
            'app_key' => $config['app_key'],
        ], $config);
    }
}
