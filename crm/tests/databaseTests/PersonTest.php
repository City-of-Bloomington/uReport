<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Application\Models\Action;

require_once './DatabaseTestCase.php';

class PersonTest extends DatabaseTestCase
{
	public function getDataSet()
	{
		return $this->createMySQLXMLDataSet(__DIR__.'/testData/people.xml');
	}

	public function testMerge()
	{
	}
}
