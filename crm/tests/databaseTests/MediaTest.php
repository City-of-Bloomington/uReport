<?php
/**
 * @copyright 2013-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Application\Models\Media;

require_once './DatabaseTestCase.php';

class MediaTest extends DatabaseTestCase
{
	private $testIssueId = 1;

	public function getDataSet()
	{
		return $this->createMySQLXMLDataSet(__DIR__.'/testData/mediaTestData.xml');
	}


	public function testSaveAndDelete()
	{
		$testFile = __DIR__.'/test.png';
		copy(__DIR__.'/../integrationTests/Dan.png', $testFile);

		$media = new Media();
		$media->setIssue_id($this->testIssueId);
		$media->setFile(array('tmp_name'=>$testFile,'name'=>'Dan.png'));
		$media->save();

		$newFile = APPLICATION_HOME."/data/media/{$media->getDirectory()}/{$media->getInternalFilename()}";
		$this->assertTrue(file_exists($newFile));
		$this->assertEquals($this->testIssueId, $media->getIssue_id());
		$this->assertNotEmpty($media->getId());

		$media->delete();
		$this->assertFalse(file_exists($newFile));
	}
}
