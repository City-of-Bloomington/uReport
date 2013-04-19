<?php
/**
 * HTML for displaying a thumbnail
 *
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Media $this->media
 */
echo "
<div class=\"thumbnail\">
	<div class=\"image\">
		<a href=\"{$this->media->getURL()}\">
			<img src=\"{$this->media->getURL(THUMBNAIL_SIZE)}\" />
		</a>
	</div>
	<div class=\"date\">{$this->media->getUploaded(DATE_FORMAT)}</div>
</div>
";