<?php
namespace Src;

class RowResult
{
    private $url;
    private $name;
    private $priceWithCurrency;
    private $pageLoadTime;

    /**
     * RowResult constructor.
     * @param $link
     * @param $name
     * @param $priceWithCurrency
     * @param $pageLoadTime
     */
    public function __construct($link, $name, $priceWithCurrency, $pageLoadTime)
    {
        $this->url = $link;
        $this->name = $name;
        $this->priceWithCurrency = $priceWithCurrency;
        $this->pageLoadTime = $pageLoadTime;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPriceWithCurrency()
    {
        return $this->priceWithCurrency;
    }

    /**
     * @return float
     */
    public function getPageLoadTime()
    {
        return $this->pageLoadTime;
    }

    /**
     * @return float
     */
    public function getPriceForSort()
    {
        return (float) $this->priceWithCurrency;
    }
}