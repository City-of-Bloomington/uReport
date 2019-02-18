<?php
/**
 * @copyright 2013-2019 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Test\Integration;

use PHPUnit\Framework\TestCase;

use Application\Models\Media;
use Application\Models\Image;
use Application\Database;

class MediaTest extends TestCase
{
	private $testSize = 60;

	/**
	 * Make sure the URL for the image is web accessible
	 */
	public function testGetURL()
	{
		$temp = __DIR__."/temp.png";

		$zend_db = Database::getConnection();
		$result = $zend_db->query("select * from media where mime_type like 'image%' limit 1")->execute();
		if (count($result)) {
			$row = $result->current();
			$media = new Media($row);
			$image = new Image($media);

			$request = curl_init($image->getURL());
			curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($request, CURLOPT_BINARYTRANSFER, true);
			file_put_contents($temp, curl_exec($request));
			$this->assertTrue(file_exists($temp), 'No file was downloaded');

			$this->assertEquals($image->getFilesize(), filesize($temp), 'Downloaded file size does not match original');

			$download = getimagesize($temp);
			$this->assertEquals($image->getWidth() , $download[0], 'Downloaded image width does not match original');
			$this->assertEquals($image->getHeight(), $download[1], 'Downloaded image height does not match original');
		}

		#if (file_exists($temp)) { unlink($temp); }
	}

	/**
	 * The thumbnails should be created automatically when first requested
	 *
	 * This is kind of like the Apache 404 trick, except we're sending
	 * all traffic to index.php, so it's handled there, instead of a 404
	 */
	public function testAutogenerateThumbnailsByURL()
	{
		$temp = __DIR__."/temp.png";

		$zend_db = Database::getConnection();
		$result = $zend_db->query("select * from media where mime_type like 'image%' limit 1")->execute();
		if (count($result)) {
			$row = $result->current();
			$media = new Media($row);
			$image = new Image($media);

			$image->clearCache();
			$this->assertFalse(file_exists($image->getFullPathForSize($this->testSize)));

			$url = $image->getURL($this->testSize);
			$request = curl_init($image->getURL($this->testSize));
			curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($request, CURLOPT_BINARYTRANSFER, true);
			file_put_contents($temp, curl_exec($request));
			$this->assertTrue(file_exists($temp));

			$info = getimagesize($temp);

			$this->assertTrue(($info[0]==$this->testSize || $info[1]==$this->testSize));

			#if (file_exists($temp)) { unlink($temp); }
		}
	}
}
