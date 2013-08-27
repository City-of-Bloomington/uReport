<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
require_once './configuration.inc';
class TicketsControllerTest extends PHPUnit_Framework_TestCase
{
	public function testAdd()
	{
		$_SESSION['USER'] = new Person();

		$template = new Template();
		$controller = new TicketsController($template);
		$controller->add();

		$this->assertGreaterThan(0,count($template->blocks));
	}
}
