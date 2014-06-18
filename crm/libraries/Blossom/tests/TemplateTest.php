<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$_SERVER['SITE_HOME'] = __DIR__;
require_once realpath(__DIR__.'/../../../configuration.inc');

use Blossom\Classes\Template;

class TemplateTest extends PHPUnit_Framework_TestCase
{
	public function testVars()
	{
		$template = new Template('default', 'html', ['test'=>'something']);
		$this->assertEquals('something', $template->test);

		$template->one = 'another';
		$this->assertEquals('another', $template->one);
	}

	/**
	 * SITE_HOME sites should be able to override any template
	 */
	public function testSiteOverrides()
	{
		$template = new Template('test', 'test');

		$expectedOutput = file_get_contents(__DIR__.'/templates/test/test.inc');
		$this->assertEquals($expectedOutput, $template->render());

		$helper = $template->getHelper('test');
		$this->assertEquals('something', $helper->test('something'));

		$template = new Template('partials', 'test');
		$expectedOutput = file_get_contents(__DIR__.'/templates/test/partials/testPartial.inc');
		$this->assertEquals($expectedOutput, $template->render());
	}
}
