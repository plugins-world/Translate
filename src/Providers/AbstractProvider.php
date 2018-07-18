<?php

namespace Yan\Translate\Providers;

use Symfony\Component\HttpFoundation\Request;
use Yan\Translate\Contracts\ProviderInterface;
use Yan\Translate\Supports\Config;
use Yan\Translate\Traits\HasHttpRequest;

/**
 * Class AbstractProvider.
 */
abstract class AbstractProvider implements ProviderInterface
{
    use HasHttpRequest;

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
     * The HTTP request instance.
     *
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * The app id.
     *
     * @var string
     */
    protected $appId;

    /**
     * The app key.
     *
     * @var string
     */
    protected $appKey;

    /**
     * AbstractProvider constructor.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param                                           $app_id
     * @param                                           $app_key
     * @param array                                     $config
     */
    public function __construct(Request $request, $app_id, $app_key, array $config)
    {
        $this->appId = $app_id;
        $this->appKey = $app_key;

        $this->config = new Config($config);
    }

    /**
     * Get the translate URL for the provider.
     *
     * @return string
     */
    protected function getTranslateUrl(): string
    {
        return $this->config->get('ssl', false) ? static::HTTPS_URL : static::HTTP_URL;
    }

    /**
     * @param array $args
     *
     * @return array
     */
    abstract protected function getRequestParams(array $args);

    /**
     * @param array $params
     *
     * @return string
     */
    abstract protected function makeSignature(array $params);

    /**
     * {@inheritdoc}
     */
    abstract public function translate(string $string, $from = 'zh', $to = 'en');

    /**
     * @param array $translateResult
     *
     * @return array
     */
    abstract protected function mapTranslateResult(array $translateResult): array;

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
     * Return array item by key.
     *
     * @param array  $array
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    protected function arrayItem(array $array, $key, $default = null)
    {
        if (is_null($key)) {
            return $array;
        }

        if (isset($array[$key])) {
            return $array[$key];
        }

        foreach (explode('.', $key) as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return $default;
            }

            $array = $array[$segment];
        }

        return $array;
    }
}
