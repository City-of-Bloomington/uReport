<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$_SERVER['SITE_HOME'] = __DIR__;
require_once realpath(__DIR__.'/../../../configuration.inc');

class ConfigurationTest extends PHPUnit_Framework_TestCase
{
	public function testSiteConfigLoaded()
	{
		$this->assertEquals('testApp', APPLICATION_NAME);
	}
}
