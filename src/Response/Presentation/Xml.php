<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Response\Presentation;

/**
 * Xml
 *
 * @package  Bluz\Response\Presentation
 *
 * @author   Anton Shevchuk
 * @created  10.11.2014 18:03
 */
class Xml extends AbstractPresentation
{
    /**
     * Response as XML
     */
    public function process()
    {
        // override response code so javascript can process it
        $this->response->setHeader('Content-Type', 'application/xml');

        // prepare body
        if ($body = $this->response->getBody()) {
            // it's fast convert from Objects to Array
            $body = json_decode(json_encode($body), true);

            $xml = new \SimpleXMLElement('<root/>');

            $body = $this->toXml($body, $xml);

            // setup content length
            $this->response->setHeader('Content-Length', strlen($body));

            // prepare to XML output
            $this->response->setBody($body);
        }
    }

    /**
     * Array to XML format converter, recursively
     *
     * @param array $data
     * @param \SimpleXMLElement $xml
     * @return string
     */
    protected function toXml($data, $xml)
    {
        // loop through the data passed in
        foreach ($data as $key => $value) {
            // no numeric keys in our xml
            if (is_numeric($key)) {
                // just hardcoded `item`
                $key = "item";
            }

            // sanitize key - replace anything not alpha numeric
            $key = preg_replace('/[^a-z]/i', '', $key);

            // if there is another array found recursively call this function
            if (is_array($value)) {
                $node = $xml->addChild($key);
                // recursive call
                $this->toXml($value, $node);
            } else {
                // add single node
                $value = htmlentities($value);
                $xml->addChild($key, $value);
            }
        }
        return $xml->asXML();
    }
}
