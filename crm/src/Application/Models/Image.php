<?php
/**
 * @copyright 2007-2016 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Models;

use Blossom\Classes\ActiveRecord;
use Blossom\Classes\Database;

class Image extends Media
{
	public function __construct(Media $media)
	{
		$this->data = $media->data;

		if ($this->getMedia_type() != 'image') {
			throw new \Exception('media/nonimage');
		}
	}

	/**
	 * Generates, caches, and outputs smaller versions of images
	 *
	 * @param int $size Bounding box in pixels
	 */
	public function output($size)
	{
		// If they don't specify size, just output the opriginal file
		$path     = $this->getFullPath();
		$filename = $this->getInternalFilename();
		if (!$size) {
            readfile($path);
		}
		else {
			$size = (int)$size;
			$thumbnailDirectory = dirname($path)."/$size";

			if (!is_file("$thumbnailDirectory/$filename")) {
				self::resize($path, $size);
			}

			readfile("$thumbnailDirectory/$filename");
		}
	}

	/**
	 * Use ImageMagick to create a thumbnail file for the given image
	 *
	 * Input must be a full path.
	 * The resized image file will be saved in $inputPath/$size/$inputFilename.$ext
	 * The sizes array determines the output filetype (gif,jpg,png)
	 * ie. /var/www/sites/photobase/uploads/username/something.jpg
	 *
	 * @param string $inputFile Full path to an image file
	 * @param int $size The desired bounding box size
	 */
	public static function resize($inputFile, $size)
	{
		$size = (int)$size;
		$directory = dirname($inputFile)."/$size";
		$filename  = basename($inputFile);

		if (!is_dir($directory)) { mkdir($directory, 0777, true); }

		$dimensions = $size.'x'.$size;
		$newFile = "$directory/$filename";
		exec(IMAGEMAGICK."/convert $inputFile -auto-orient -resize '$dimensions' png:$newFile");
	}

	/**
	 * Delete any cached preview version of this image
	 */
	public function clearCache()
	{
		$uniqid  = $this->getInternalFilename();
		$dir     = dirname($this->getFullPath());
		$pattern = "$dir/*/$uniqid";

		foreach(glob($pattern) as $file) {
			unlink($file);
		}
	}

	/**
	 * @param int $size
	 * @return string
	 */
	public function getFullPathForSize($size=null)
	{
		$size     = (int)$size;
		$dir      = dirname($this->getFullPath());
		$filename = $this->getInternalFilename();

		return $size
            ? "$dir/$size/$filename"
            : "$dir/$filename";
	}

	/**
	 * Returns the width of the requested version of an image
	 *
	 * @param string $size The version of the image (see self::$sizes)
	 */
	public function getWidth($size=null)
	{
		return exec(IMAGEMAGICK."/identify -format '%w' ".$this->getFullPathForSize($size));
	}

	/**
	 * Returns the height of the requested version of an image
	 *
	 * @param string $size The version of the image (see self::$sizes)
	 */
	public function getHeight($size=null)
	{
		return exec(IMAGEMAGICK."/identify -format '%h' ".$this->getFullPathForSize($size));
	}
}
