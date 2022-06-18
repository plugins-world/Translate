<?php

namespace MouYong\Translate;

use ZhenMu\Support\Traits\Arrayable;

/**
 * Undocumented class
 */
class LanguageRecognizer implements \ArrayAccess
{
    use Arrayable;

    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    public function getDetectContent()
    {
        return $this['detectContent'];
    }

    /**
     * current country and area is ISO 639-3
     * 
     * @return void
     * 
     * @see https://www.iso.org/iso-639-language-codes.html
     * 
     * @see ISO 639-1: https://fresns.cn/database/dictionary/language-codes.html
     */
    protected function getCountryAndArea()
    {
        [$countryCode, $areaCode] = explode('-', $this['code']);

        return [
            'countryCode' => $countryCode,
            'areaCode' => $areaCode,
        ];
    }

    /**
     * @return string
     * 
     * @see https://fresns.cn/database/dictionary/area-codes.html
     */
    public function getCountryCode()
    {
        return $this->getCountryAndArea()['countryCode'];
    }

    /**
     * @return string
     * 
     * @see https://fresns.cn/database/dictionary/area-codes.html
     */
    public function getAreaCode()
    {
        return $this->getCountryAndArea()['areaCode'];
    }

    public function getLanguage()
    {
        return $this['language'];
    }

    public function getEthnologue()
    {
        return $this['ethnologue'];
    }

    public function getEthno3()
    {
        return trim($this->getEthnologue()['ethno-3']);
    }

    public function getEthnoUrl()
    {
        return trim($this->getEthnologue()['url']);
    }

    public function getOriginal()
    {
        return $this->toArray();
    }

    public function getData()
    {
        return [
            'detectContent' => $this->getDetectContent(),
            'countryCode' => $this->getCountryCode(),
            'areaCode' => $this->getAreaCode(),
            'language' => $this->getLanguage(),
            'ethno-3' => $this->getEthno3(),
            'ethno-url' => $this->getEthnoUrl(),
            'orininal' => $this->getOriginal(),
        ];
    }
}
