<?php

/**
 * PHP version 7.1
 *
 * @package Agentzilla\Collection
 */

namespace Agentzilla\Collection;

use \Agentzilla\HTTP\HTTPclient;
use \SimpleXMLElement;
use \AdService\XMLGenerator;

/**
 * Page object
 *
 * @author  Andrey Mashukov <a.mashukoff@gmail.com>
 * @version SVN: $Date: 2018-02-10 18:40:38 +0000 (Sat, 10 Feb 2018) $ $Revision: 1 $
 * @link    $HeadURL: https://svn.agentzilla.ru/collection/trunk/src/Page.php $
 */

class Page
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
	 * Operation
	 *
	 * @var string
	 */
	public $link = null;

	/**
	 * Operation
	 *
	 * @var string
	 */
	public $html = null;

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
	 * @var XMLGenerator
	 */
	private $_xml;

	/**
	 * Doc
	 *
	 * @var DOMDocument
	 */
	public $doc = null;

	/**
	 * Prepare
	 *
	 * @param SimpleXMLElement $link XML link
	 *
	 * @return void
	 */

	public function __construct(SimpleXMLElement $link)
	    {
		$this->_xml = new XMLGenerator("page");

		$props = ["country", "region", "city", "site", "type", "operation", "link", "headers", "html"];

		foreach ($props as $prop)
		    {
			if (empty($link->$prop) === false)
			    {
				$this->_xml->newElement($prop, (string) $link->$prop);
				$this->$prop = (string) $link->$prop;
			    } //end if

		    } //end foreach

		if (empty($link->{"headers"}) === false)
		    {
			$this->headers = unserialize(base64_decode((string) $link->headers));
		    } //end if

		$secure = ["gzdecode", "proxy"];

		foreach ($secure as $element)
		    {
			if (empty($link->{$element}) === false && (string) $link->{$element} === "Y")
			    {
				$this->$element = true;
			    } //end if

		    } //end foreach

		$this->doc = $this->_xml->getDoc();
	    } //end __construct()


	/**
	 * Load page HTML code from source
	 *
	 * @return void
	 */

	public function loadHtml()
	    {
		$http = new HTTPclient($this->link, [], $this->headers);
		if ($this->proxy === true)
		    {
			$this->html = $http->getWithProxy();
		    }
		else
		    {
			$this->html = $http->get();
		    } //end if

		if ($this->gzdecode === true)
		    {
			$this->html = gzdecode($this->html);
		    } //end if

		$this->_xml->newElement("html", base64_encode($this->html));
		$this->doc = $this->_xml->getDoc();
	    } //end loadHtml()


	/**
	 * ToString method, get XML Page
	 *
	 * @return string XML
	 */

	public function __toString():string
	    {
		return $this->doc->saveXML();
	    } //end __toString()


	/**
	 * Validate page by XML schema
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
