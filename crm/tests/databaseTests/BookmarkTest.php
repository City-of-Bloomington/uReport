<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Application\Models\Bookmark;
use Application\Models\Person;

require_once './DatabaseTestCase.php';

class BookmarkTest extends DatabaseTestCase
{
	private $testPersonId = 1;

	public function getDataSet()
	{
		return $this->createMySQLXMLDataSet(__DIR__.'/testData/bookmarks.xml');
	}

	public function testSaveAndLoad()
	{
		$_SESSION['USER'] = new Person($this->testPersonId);

		$bookmark = new Bookmark();
		$bookmark->setRequestUri('/test');
		$bookmark->save();

		$id = $bookmark->getId();
		$this->assertNotEmpty($id);

		$bookmark = new Bookmark($id);
		$this->assertEquals($this->testPersonId, $bookmark->getPerson_id());
		$this->assertEquals('/test', $bookmark->getRequestUri());
	}
}
