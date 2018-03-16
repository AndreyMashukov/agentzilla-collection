<?php

/**
 * PHP version 7.1
 *
 * @package Agentzilla\Collection
 */

namespace Agentzilla\Collection;

use \SimpleXMLElement;
use \AdService\XMLGenerator;

/**
 * Link object
 *
 * @author  Andrey Mashukov <a.mashukoff@gmail.com>
 * @version SVN: $Date: 2018-02-10 18:40:38 +0000 (Sat, 10 Feb 2018) $ $Revision: 1 $
 * @link    $HeadURL: https://svn.agentzilla.ru/collection/trunk/src/Link.php $
 */

class Link
    {

	/**
	 * Country
	 *
	 * @var string
	 */
	public $country = null;

	/**
	 * Region
	 *
	 * @var string
	 */
	public $region = null;

	/**
	 * City
	 *
	 * @var string
	 */
	public $city = null;

	/**
	 * Site
	 *
	 * @var string
	 */
	public $site = null;

	/**
	 * Type
	 *
	 * @var string
	 */
	public $type = null;

	/**
	 * Operation
	 *
	 * @var string
	 */
	public $operation = null;

	/**
	 * Headers
	 *
	 * @var array
	 */
	public $headers = [];

	/**
	 * GzDecode
	 *
	 * @var bool
	 */
	public $gzdecode = false;

	/**
	 * Proxy
	 *
	 * @var bool
	 */
	public $proxy = false;

	/**
	 * Doc
	 *
	 * @var DOMDocument
	 */
	public $doc = null;

	/**
	 * XML
	 *
	 * @var XMLGenerator
	 */
	protected $xml = null;

	/**
	 * Prepare
	 *
	 * @param string           $link   Link
	 * @param SimpleXMLElement $config Grabber config
	 * @param SimpleXMLElement $params current category parameters
	 *
	 * @return void
	 */

	public function __construct(string $link, SimpleXMLElement $config, SimpleXMLElement $params)
	    {
		$this->xml = new XMLGenerator("link");
		$this->xml->newElement("country",   (string) $config->{"country"});
		$this->xml->newElement("region",    (string) $config->{"region"});
		$this->xml->newElement("city",      (string) $config->{"city"});
		$this->xml->newElement("site",      (string) $config->{"site"});
		$this->xml->newElement("type",      (string) $params["type"]);
		$this->xml->newElement("operation", (string) $params["operation"]);
		$this->xml->newElement("link",      $link);

		if (empty($config->{"headers"}) === false)
		    {
			foreach ($config->{"headers"}->{"header"} as $header)
			    {
				$this->headers[(string) $header["name"]] = (string) $header;
			    }
		    } //end if

		if (count($this->headers) > 0)
		    {
			$head = base64_encode(serialize($this->headers));
			$this->xml->newElement("headers", $head);
		    } //end if

		$secure = ["gzdecode", "proxy"];

		foreach ($secure as $element)
		    {
			if (empty($config->{$element}) === false && (string) $config->{$element} === "Y")
			    {
				$this->xml->newElement($element, "Y");
				$this->$element = true;
			    } //end if

		    } //end foreach

		$this->doc = $this->xml->getDoc();
	    } //end __construct()


	/**
	 * ToString method, get XML Link
	 *
	 * @return string XML
	 */

	public function __toString():string
	    {
		return $this->doc->saveXML();
	    } //end __toString()


	/**
	 * Validate link by XML schema
	 *
	 * @param string $path Path to XML Schema
	 *
	 * @return bool Result
	 */

	public function validate(string $path):bool
	    {
		return $this->doc->schemaValidate($path);
	    } //end validate()


    } //end class

?>
