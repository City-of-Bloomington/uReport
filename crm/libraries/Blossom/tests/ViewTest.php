<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$_SERVER['SITE_HOME'] = __DIR__;
require_once realpath(__DIR__.'/../../../configuration.inc');

use Blossom\Classes\View;

class ViewTest extends PHPUnit_Framework_TestCase
{
	public function testVars()
	{
		$view = new ViewStub(['test'=>'something']);
		$this->assertEquals('something', $view->test);

		$view->one = 'another test';
		$this->assertEquals('another test', $view->one);
	}
}

class ViewStub extends View
{
	public function __construct($vars=null)
	{
		parent::__construct($vars);
	}

	public function render() { return 'test content'; }
}
