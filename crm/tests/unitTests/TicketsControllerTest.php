<?php
/**
 * @copyright 2013-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Application\Controllers\TicketsController;
use Application\Models\Person;
use Blossom\Classes\Template;

$_SERVER['SITE_HOME'] = __DIR__;
require_once '../../configuration.inc';

class TicketsControllerTest extends PHPUnit_Framework_TestCase
{
	public function testAdd()
	{
		$_SESSION['USER'] = new Person();

		$template = new Template();
		$controller = new TicketsController($template);
		$controller->add();

		$this->assertGreaterThan(0, count($template->blocks));
	}
}
