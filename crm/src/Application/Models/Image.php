<?php
/**
 * @copyright 2007-2026 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models;

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
     *
     * @param string $inputFile Full path to an image file
     * @param int    $size      Pixel width of the square bounding box
     */
    public static function resize(string $inputFile, int $size)
    {
        $directory = dirname($inputFile)."/$size";
        $filename  = basename($inputFile);

        if (!is_dir($directory)) { mkdir($directory, 0777, true); }

        $dimensions = $size.'x'.$size;
        $newFile = "$directory/$filename";
        exec("convert $inputFile -auto-orient -resize '$dimensions' png:$newFile");
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

    public function getFullPathForSize(?int $size=null): string
    {
        $size     = (int)$size;
        $dir      = dirname($this->getFullPath());
        $filename = $this->getInternalFilename();

        return $size
            ? "$dir/$size/$filename"
            : "$dir/$filename";
    }

    public function getWidth(): int
    {
        return (int)exec("identify -format '%w' ".$this->getFullPath());
    }

    public function getHeight(): int
    {
        return (int)exec("identify -format '%h' ".$this->getFullPath());
    }
}
