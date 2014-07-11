<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$_SERVER['SITE_HOME'] = __DIR__;
require_once realpath(__DIR__.'/../../../configuration.inc');

use Blossom\Classes\Url;

class UrlTest extends PHPUnit_Framework_TestCase
{

	public function testUrlOutput()
	{
		$testUrl = 'http://www.somewhere.com/test';

		$url = new Url($testUrl);
		$this->assertEquals($testUrl, "$url");
	}

	public function testChangeScheme()
	{
		$url = new Url('http://www.somewhere.com');
		$url->setScheme('webcal://');
		$this->assertEquals('webcal://www.somewhere.com', "$url");
	}

	public function testUrlWithoutScheme()
	{
		$url = new Url('bloomington.in.gov/test');
		$this->assertEquals('http', $url->getScheme());
		$this->assertEquals('http://bloomington.in.gov/test', "$url");
	}
}
