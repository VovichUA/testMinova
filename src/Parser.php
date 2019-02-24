<?php

namespace Src;

require_once 'RowResult.php';

use DOMDocument;
use DOMElement;
use DOMXPath;

class Parser
{
    const PRODUCT_BOX_CLASS = 'product-box1';
    const PRODUCT_CONTAINER_HOLDER_CLASS = 'product-content-holder';

    private $url;
    private $urlHost;
    private $urlSchema;
    /** @var RowResult[] $productsData */
    private $productsData = [];

    public function __construct($url)
    {
        $this->validateUrl($url);
        $this->url = $url;
    }

    public function parse()
    {
        $dom = new DOMDocument();
        $dom->loadHTML(file_get_contents($this->url));

        $finder = new DOMXPath($dom);
        $expression = "//*[contains(@class, '" . self::PRODUCT_BOX_CLASS . "')]";
        $nodes = $finder->query($expression);

        foreach ($nodes as $node) {
            foreach ($node->childNodes as $childNode) {
                if (($childNode instanceof DOMElement) && ($rowResult = $this->getRowResultFormDomElement($childNode))) {
                    $this->productsData[] = $rowResult;
                }
            }
        }
        $this->sortData();
        $this->generateHtmlFile();
    }

    private function validateUrl($url)
    {
        $this->urlHost = parse_url($url, PHP_URL_HOST);
        if ($this->urlHost == null) {
            throw new \Exception('Некорректный URL-адрес');
        }
        $this->urlSchema = parse_url($url, PHP_URL_SCHEME);
    }

    private function getPageLoadTime($url)
    {
        $curlResource = curl_init($url);
        curl_setopt($curlResource, CURLOPT_RETURNTRANSFER, true);
        curl_exec($curlResource);
        $pageLoadTime = curl_getinfo($curlResource, CURLINFO_TOTAL_TIME);
        curl_close($curlResource);

        return $pageLoadTime;
    }

    private function getRowResultFormDomElement(DOMElement $DOMElement)
    {
        $rowResult = null;
        $childNodeClassName = trim($DOMElement->getAttribute('class'));
        if ($childNodeClassName == self::PRODUCT_CONTAINER_HOLDER_CLASS) {
            $productATag = $DOMElement->getElementsByTagName('a')->item(0);
            $productLink = $this->urlHost . $productATag->getAttribute('href');
            $productName = $productATag->nodeValue;
            $productPriceWithCurrency = null;
            foreach ($DOMElement->getElementsByTagName('span') as $productSpan) {
                $currentSpanClassName = $productSpan->getAttribute('class');
                if ($currentSpanClassName == '') {
                    $productPriceWithCurrency = trim($productSpan->nodeValue);
                }
            }

            $pageLoadTime = $this->getPageLoadTime($productLink);

            $rowResult = new RowResult($productLink, $productName, $productPriceWithCurrency, $pageLoadTime);
        }
        return $rowResult;
    }

    private function sortData()
    {
        usort($this->productsData, function ($item1, $item2) {
            /**
             * @var RowResult $item1
             * @var RowResult $item2
             */
            return $item1->getPriceForSort() <=> $item2->getPriceForSort();
        });
    }

    private function generateHtmlFile()
    {
        $fileName = $this->urlHost . "_" . date('d.m.y_H:i:s') . ".html";
        $templateHtml = file_get_contents(__DIR__ . '/resultTemplate.html');
        $productsDataHtml = '';
        foreach ($this->productsData as $productsDatum) {
            $productLink = "<a href='{$this->urlSchema}://{$productsDatum->getUrl()}' target='_blank'>{$productsDatum->getUrl()}</a>";
            $currentRow = "<tr>
                                <td>{$productLink}</td>
                                <td>{$productsDatum->getName()}</td>
                                <td>{$productsDatum->getPriceWithCurrency()}</td>
                                <td>{$productsDatum->getPageLoadTime()}</td>
                           </tr>";
            $productsDataHtml .= $currentRow;
        };
        $resultHtml = str_replace('{productsData}', $productsDataHtml, $templateHtml);

        header('Content-disposition: attachment; filename=' . $fileName);
        header('Content-type: text/html');
        echo $resultHtml;
    }
}