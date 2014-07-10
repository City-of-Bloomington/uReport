<?php
/**
 * @copyright 2013-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Application\Models\Media;
use Application\Models\Image;

require_once './DatabaseTestCase.php';

class MediaTest extends DatabaseTestCase
{
	private $testIssueId = 1;
	private $origFile = '';
	private $testFile = '';
	private $FILE = array(); // stand-in for $_FILE
	private $testSize = 60;

	public function __construct()
	{
		$this->origFile = __DIR__.'/testData/Dan.png';
		$this->testFile = __DIR__.'/test.png';
		$this->FILE = ['tmp_name'=>$this->testFile,'name'=>'Dan.png'];
	}

	public function getDataSet()
	{
		return $this->createMySQLXMLDataSet(__DIR__.'/testData/media.xml');
	}

	public function testSetFile()
	{
		copy($this->origFile, $this->testFile);

		$media = new Media();
		$media->setFile($this->FILE);

		$newFile = SITE_HOME."/media/{$media->getDirectory()}/{$media->getInternalFilename()}";
		$this->assertTrue(file_exists($newFile));

		if (file_exists($newFile)) { unlink($newFile); }
	}

	public function testGenerateAndClearThumbnail()
	{
		copy($this->origFile, $this->testFile);

		$media = new Media();
		$media->setFile($this->FILE);

		$image = new Image($media);

		ob_start();
		$image->output($this->testSize);
		ob_end_clean();

		$newFile   = SITE_HOME."/media/{$media->getDirectory()}/{$media->getInternalFilename()}";
		$thumbnail = SITE_HOME."/media/{$media->getDirectory()}/{$this->testSize}/{$media->getInternalFilename()}";
		$this->assertTrue(file_exists($thumbnail));

		$info = getimagesize($thumbnail);
		$this->assertTrue(($info[0]==$this->testSize || $info[1]==$this->testSize));

		$image->clearCache();
		$this->assertFalse(file_exists($thumbnail));

		if (file_exists($thumbnail)) { unlink($thumbnail); }
		if (file_exists($newFile  )) { unlink($newFile);   }
	}

	public function testSaveAndDelete()
	{
		copy($this->origFile, $this->testFile);

		$media = new Media();
		$media->setIssue_id($this->testIssueId);
		$media->setFile($this->FILE);
		$media->save();

		$newFile = SITE_HOME."/media/{$media->getDirectory()}/{$media->getInternalFilename()}";
		$this->assertTrue(file_exists($newFile));
		$this->assertEquals($this->testIssueId, $media->getIssue_id());
		$this->assertNotEmpty($media->getId());

		$media->delete();
		$this->assertFalse(file_exists($newFile));
	}
}
