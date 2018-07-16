<?php

namespace Yan\Translate;

use Closure;
use RuntimeException;
use Yan\Translate\Contracts\TranslateInterface;
use Yan\Translate\Exceptions\InvalidArgumentException;
use Yan\Translate\Support\Collection;

class Translate
{
    protected $config;

    protected $defaultDriver;

    protected $customDrivers = [];

    protected $drivers = [];

    public function __construct(array $config = [])
    {
        $this->config = new Collection($config);

        if (!empty($config['default'])) {
            $this->setDefaultDriver($config['default']);
        }
    }

    public function translate($content, $from = null, $to = null)
    {
        return $this->driver()->translate($content, $from, $to);
    }

    /**
     * @param null $name
     *
     * @return TranslateInterface
     *
     * @throws \Yan\EasyTranslate\Exceptions\InvalidArgumentException
     */
    public function driver($name = null)
    {
        $name = $name ?: $this->getDefaultDriver();

        if (!isset($this->drivers[$name])) {
            $this->drivers[$name] = $this->createDriver($name);
        }

        return $this->drivers[$name];
    }

    public function extend($name, Closure $callback)
    {
        $this->customDrivers[$name] = $callback;

        return $this;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getDefaultDriver()
    {
        if (empty($this->defaultDriver)) {
            throw new RuntimeException();
        }

        return $this->defaultDriver;
    }

    public function setDefaultDriver($name)
    {
        $this->defaultDriver = $name;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return mixed
     *
     * @throws \Yan\EasyTranslate\Exceptions\InvalidArgumentException
     */
    protected function createDriver(string $name)
    {
        if (isset($this->customDrivers[$name])) {
            $driver = $this->callCustomDriver($name);
        } else {
            $className = $this->formatDriverClassName($name);
            $driver = $this->makeDriver($className, $this->config->get("drivers.{$name}", []));
        }

        if (!($driver instanceof TranslateInterface)) {
            throw new InvalidArgumentException(sprintf('Driver "%s" not inherited from %s.', $name, TranslateInterface::class));
        }

        return $driver;
    }

    protected function makeDriver($driver, $config)
    {
        if (!class_exists($driver)) {
            throw new InvalidArgumentException(sprintf('Driver "%s" not exists.', $driver));
        }

        return new $driver($config);
    }

    protected function formatDriverClassName($name)
    {
        if (class_exists($name)) {
            return $name;
        }

        $name = ucfirst(str_replace(['-', '_', ''], '', $name));

        return __NAMESPACE__."\\Drivers\\{$name}";
    }

    protected function callCustomDriver($driver)
    {
        return call_user_func($this->customDrivers[$driver], $this->config->get("drivers.{$driver}", []));
    }
}
