<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
require_once __DIR__.'/../../configuration.inc';

class MediaTest extends PHPUnit_Framework_TestCase
{
	private $origFile = '';
	private $testFile = '';
	private $FILE = array(); // stand-in for $_FILE
	private $testSize = 60;

	public function __construct()
	{
		$this->origFile = __DIR__.'/Dan.png';
		$this->testFile = __DIR__.'/test.png';
		$this->FILE = array('tmp_name'=>$this->testFile,'name'=>'Dan.png');
	}

	/**
	 * Creates a fresh test image to use as a file upload
	 *
	 * Required, because the file upload is moved, not copied.
	 * In other words, the file gets deleted each time
	 */
	public function setUp()
	{
		copy($this->origFile, $this->testFile);
	}

	public function testSetFile()
	{
		$media = new Media();
		$media->setFile($this->FILE);

		$newFile = CRM_DATA_HOME."/data/media/{$media->getDirectory()}/{$media->getInternalFilename()}";
		$this->assertTrue(file_exists($newFile));

		if (file_exists($newFile)) { unlink($newFile); }
	}

	public function testGenerateAndClearThumbnail()
	{
		$media = new Media();
		$media->setFile($this->FILE);

		$image = new Image($media);

		ob_start();
		$image->output($this->testSize);
		ob_end_clean();

		$newFile   = CRM_DATA_HOME."/data/media/{$media->getDirectory()}/{$media->getInternalFilename()}";
		$thumbnail = CRM_DATA_HOME."/data/media/{$media->getDirectory()}/{$this->testSize}/{$media->getInternalFilename()}";
		$this->assertTrue(file_exists($thumbnail));

		$info = getimagesize($thumbnail);
		$this->assertTrue(($info[0]==$this->testSize || $info[1]==$this->testSize));

		$image->clearCache();
		$this->assertFalse(file_exists($thumbnail));

		if (file_exists($thumbnail)) { unlink($thumbnail); }
		if (file_exists($newFile)) { unlink($newFile); }
	}

	/**
	 * Make sure the URL for the image is web accessible
	 */
	public function testGetURL()
	{
		$media = new Media();
		$media->setFile($this->FILE);

		$newFile   = CRM_DATA_HOME."/data/media/{$media->getDirectory()}/{$media->getInternalFilename()}";

		$temp = __DIR__."/temp.png";
		$request = curl_init($media->getURL());
		curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($request, CURLOPT_BINARYTRANSFER, true);
		file_put_contents($temp, curl_exec($request));
		$this->assertTrue(file_exists($temp));

		$origInfo = getimagesize($this->origFile);
		$download = getimagesize($temp);

		$this->assertEquals($origInfo[0], $download[0]);
		$this->assertEquals($origInfo[1], $download[1]);

		if (file_exists($newFile)) { unlink($newFile); }
		if (file_exists($temp)) { unlink($temp); }
	}

	/**
	 * The thumbnails should be created automatically when first requested
	 *
	 * This is kind of like the Apache 404 trick, except we're sending
	 * all traffic to index.php, so it's handled there, instead of a 404
	 */
	public function testAutogenerateThumbnailsByURL()
	{
		$media = new Media($this->testSize);
		$media->setFile($this->FILE);

		$newFile   = CRM_DATA_HOME."/data/media/{$media->getDirectory()}/{$media->getInternalFilename()}";
		$thumbnail = CRM_DATA_HOME."/data/media/{$media->getDirectory()}/{$this->testSize}/{$media->getInternalFilename()}";

		$temp = __DIR__."/temp.png";
		$request = curl_init($media->getURL($this->testSize));
		curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($request, CURLOPT_BINARYTRANSFER, true);
		file_put_contents($temp, curl_exec($request));
		$this->assertTrue(file_exists($temp));

		$info = getimagesize($temp);
		$this->assertTrue(($info[0]==$this->testSize || $info[1]==$this->testSize));

		if (file_exists($newFile)) { unlink($newFile); }
		if (file_exists($thumbnail)) { unlink($thumbnail); }
		if (file_exists($temp)) { unlink($temp); }
	}
}
