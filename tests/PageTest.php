<?php

/**
 * PHP version 7.1
 *
 * @package Agentzilla\Collection
 */

namespace Tests;

use \Agentzilla\Collection\Page;
use \PHPUnit\Framework\TestCase;
use \SimpleXMLElement;

/**
 * Page object
 *
 * @author  Andrey Mashukov <a.mashukoff@gmail.com>
 * @version SVN: $Date: 2018-02-10 18:40:38 +0000 (Sat, 10 Feb 2018) $ $Revision: 1 $
 * @link    $HeadURL: https://svn.agentzilla.ru/collection/trunk/tests/PageTest.php $
 *
 * @runTestsInSeparateProcesses
 */

class PageTest extends TestCase
    {

	/**
	 * Prepare data for testing
	 *
	 * @return void
	 */

	public function setUp()
	    {
		define("AGENTZILLA", "https://agentzilla.ru");
		define("SERVICE_NAME", "collector");

		parent::setUp();
	    } //end setUp()


	/**
	 * Destroy testing data
	 *
	 * @return void
	 */

	public function tearDown()
	    {
		parent::tearDown();
	    } //end setUp()


	/**
	 * Should generate page object from XML
	 *
	 * @return void
	 */

	public function testShouldGeneratePageObjectFromXml()
	    {
		$headers = array(
			    "Host"                     => "m.avito.ru",
			    "Accept"                   => "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
			    "Accept-Language"          => "ru,en-US;q=0.7,en;q=0.3",
			    "Accept-Encoding"          => "gzip, deflate, br",
			    "Connection"               => "keep-alive",
			    "Upgrade-Insecure-Request" => 1,
			    "Cache-Control"            => "max-age=0",
			   );

		$xml  = new SimpleXMLElement(file_get_contents(__DIR__ . "/datasets/links/1.xml"));
		$page = new Page($xml);

		$this->assertTrue($page->proxy);
		$this->assertTrue($page->gzdecode);

		$this->assertEquals("https://m.avito.ru/irkutsk/kvartiry/sdam/?s=0", $page->link);
		$this->assertEquals($headers,             $page->headers);
		$this->assertEquals("Россия",             $page->country);
		$this->assertEquals("Иркутская область",  $page->region);
		$this->assertEquals("Иркутск",            $page->city);
		$this->assertEquals("Avito",              $page->site);
		$this->assertEquals("flat",               $page->type);
		$this->assertEquals("rent",               $page->operation);
		$this->assertNull($page->html);

		$page->loadHtml();
		$this->assertContains("Аренда квартир - снять квартиру без посредников в Иркутске на Avito", $page->html);
		$this->assertTrue($page->validate(__DIR__ . "/schemas/page.xsd"));
	    } //end testShouldGeneratePageObjectFromXml()


	/**
	 * Should construct page with html
	 *
	 * @return void
	 */

	public function testShouldConstructPageWithHtml()
	    {
		$page = new Page(new SimpleXMLElement(file_get_contents(__DIR__ . "/datasets/pages/1.xml")));
		$this->assertTrue($page->validate(__DIR__ . "/schemas/page.xsd"));
	    } //end testShouldConstructPageWithHtml()


    } //end class

?>
