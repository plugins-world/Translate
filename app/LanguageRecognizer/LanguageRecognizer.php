<?php

namespace Plugins\Translate\LanguageRecognizer;

class LanguageRecognizer
{
    protected $attributes = [];

    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    public function getDetectContent()
    {
        return $this->attributes['detectContent'] ?? null;
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
        $code = $this->attributes['code'] ?? null;

        [$countryCode, $areaCode] = explode('-', $code);

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
        return $this->attributes['language'] ?? null;
    }

    public function getEthnologue()
    {
        return $this->attributes['ethnologue'] ?? null;
    }

    public function getEthno3()
    {
        if (!$this->getEthnologue()) {
            return null;
        }

        return trim($this->getEthnologue()['ethno-3']);
    }

    public function getEthnoUrl()
    {
        if (!$this->getEthnologue()) {
            return null;
        }

        return trim($this->getEthnologue()['url']);
    }

    public function getOriginal()
    {
        return $this->attributes;
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
