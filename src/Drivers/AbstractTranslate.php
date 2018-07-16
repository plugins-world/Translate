<?php

namespace Yan\Translate\Drivers;

use Yan\Translate\Contracts\TranslateInterface;
use Yan\Translate\Exceptions\InvalidArgumentException;
use Yan\Translate\Support\Collection;
use Yan\Translate\Traits\HasHttpRequest;

abstract class AbstractTranslate implements TranslateInterface
{
    use HasHttpRequest;

    /**
     * @var array|\Yan\Translate\Support\Collection
     */
    protected $httpUrl = '';

    protected $httpsUrl = '';

    protected $config = [];

    protected $content = '';

    protected $form = 'auto';

    protected $to = 'zh';

    public function __construct(array $config)
    {
        $this->config = new Collection($config);
    }

    public function translate(string $content, $from = null, $to = null)
    {
        $this->setTranslateOption($content, $from, $to);

        $response = $this->post(
            $this->getRequestUrl(),
            $this->getQuery()
        );

        return $response;
    }

    protected function getQuery()
    {
        $salt = time();

        $query = [
            'from' => $this->form,
            'to' => $this->to,
            'appid' => $this->config->get('app_id'),
            'q' => $this->content,
            'salt' => $salt,
            'sign' => $this->generateSign($this->content, $salt),
        ];

        return $query;
    }

    abstract protected function generateSign(string $content, int $salt);

    protected function getRequestUrl()
    {
        if ($this->config->get('ssl', false)) {
            return $this->httpsUrl;
        }

        return $this->httpsUrl;
    }

    /**
     * @param string $content
     * @param string $from
     * @param string $to
     *
     * @return $this
     *
     * @throws \Yan\Translate\Exceptions\InvalidArgumentException
     */
    public function setTranslateOption(string $content, $from = 'auto', $to = 'zh')
    {
        return $this->setTranslateContent($content)
                    ->setTranslateFrom($from)
                    ->setTranslateTo($to);
    }

    /**
     * @param string $content
     *
     * @return $this
     *
     * @throws \Yan\Translate\Exceptions\InvalidArgumentException
     */
    public function setTranslateContent($content = '')
    {
        if (empty($content)) {
            throw new InvalidArgumentException();
        }

        $this->content = $content;

        return $this;
    }

    /**
     * @param string $from
     *
     * @return $this
     */
    public function setTranslateFrom($from = 'auto')
    {
        $this->from = $from ?: $this->form;

        return $this;
    }

    /**
     * @param string $to
     *
     * @return $this
     */
    public function setTranslateTo($to = 'zh')
    {
        $this->to = $to ?: $this->to;

        return $this;
    }
}
