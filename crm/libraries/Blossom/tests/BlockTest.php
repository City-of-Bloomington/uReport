<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$_SERVER['SITE_HOME'] = __DIR__;
require_once realpath(__DIR__.'/../../../configuration.inc');

use Blossom\Classes\Block;
use Blossom\Classes\Template;

class BlockTest extends PHPUnit_Framework_TestCase
{
	public function testVars()
	{
		$block = new Block('test', ['test'=>'something']);
		$this->assertEquals('something', $block->test);

		$block->one = 'another';
		$this->assertEquals('another', $block->one);
	}

	/**
	 * SITE_HOME should be able to override any block
	 */
	public function testSiteOverrides()
	{
		$template = new Template('test', 'test');
		$block = new Block('test.inc');

		$expectedOutput = file_get_contents(__DIR__.'/blocks/test/test.inc');
		$this->assertEquals($expectedOutput, $block->render('test', $template));

		$block = new Block('includes.inc');
		$this->assertEquals($expectedOutput, $block->render('test', $template));
	}
}
