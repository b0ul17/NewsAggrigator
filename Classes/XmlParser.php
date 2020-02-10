<?php

namespace Helpers;


use SimpleXMLElement;
use XMLReader;

/**
 * Class XML_PARSER
 * @package Helpers
 * required parameter of xml url
 */
class XMLPARSER
{
    Public $url;
    Public $seller_id;
    Public $eshop_title;
    Public $timeout;

    function __construct($url)
    {
        $this->url = $url;
    }


    public function getFeeds()
    {

        $xml = new XMLReader();
        $xml->open($this->url);

        while ($xml->read() && $xml->name != 'item') {
            ;
        }

        while ($xml->name == 'item') {
            $element = new SimpleXMLElement($xml->readOuterXML(), LIBXML_NOCDATA);


            yield $element;

            $xml->next('item');
            unset($element);
        }


        $xml->close();

    }



}