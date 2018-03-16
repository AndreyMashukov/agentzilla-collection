<?php

/**
 * PHP version 7.1
 *
 * @package Agentzilla\Collection
 */

namespace Tests;

use \Agentzilla\Collection\Link;
use \PHPUnit\Framework\TestCase;
use \SimpleXMLElement;

/**
 * Link test
 *
 * @author  Andrey Mashukov <a.mashukoff@gmail.com>
 * @version SVN: $Date: 2018-02-10 18:40:38 +0000 (Sat, 10 Feb 2018) $ $Revision: 1 $
 * @link    $HeadURL: https://svn.agentzilla.ru/collection/trunk/tests/LinkTest.php $
 */

class LinkTest extends TestCase
    {

	/**
	 * Should construct link object and validate by xml schema
	 *
	 * @return void
	 */

	public function testShouldConstructLinkObjectAndValidateByXmlSchema()
	    {
		$config  = new SimpleXMLElement(file_get_contents(__DIR__ . "/datasets/configs/avito_irkutsk.xml"));
		$headers = [];
		if (empty($config->{"headers"}) === false)
		    {
			foreach ($config->{"headers"}->{"header"} as $header)
			    {
				$headers[(string) $header["name"]] = (string) $header;
			    }
		    } //end if


		foreach($config->{"params"}->{"param"} as $param)
		    {
			$link = new Link("http://" . sha1(uniqid()) . ".ru", $config, $param);
			$this->assertEquals($headers, $link->headers);
			$this->assertTrue($link->validate(__DIR__ . "/schemas/link.xsd"));
		    } //end foreach

	    } //end testShouldConstructLinkObjectAndValidateByXmlSchema()


    } //end class

?>
