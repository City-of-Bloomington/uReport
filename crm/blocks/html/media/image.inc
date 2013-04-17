<?php
/**
 * Outputs an image file to the browser
 *
 * Caches a resized version of the image
 *
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Media $this->media
 * @param int $this->size
 */
$mime             = $this->media->getMime_type();
$disposition      = $this->media->getMedia_type()=='attachment' ? 'attachment' : 'inline';
$path             = $this->media->getDirectory();
$filename         = $this->media->getInternalFilename();

header("Content-type: $mime");
header("Content-Disposition: $disposition; filename=$filename");

if ($this->media->getMedia_type() == 'image') {
	$image = new Image($this->media);
	$image->output($this->size);
}