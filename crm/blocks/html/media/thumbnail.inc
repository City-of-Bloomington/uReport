<?php
/**
 * HTML for displaying a thumbnail
 *
 * @copyright 2013-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Media $this->media
 * @param bool  $this->disableButtons (optional)
 */
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;

$deleteButton = '';
if (!$this->disableButtons && Person::isAllowed('media', 'delete')) {
	$h = $this->template->getHelper('buttonLink');
	$deleteButton = $h->buttonLink(
		BASE_URI.'/media/delete?media_id='.$this->media->getId(),
		$this->_('delete'),
		'delete',
		ButtonLink::SIZE_ICON
	);
}

$img = $this->media->getMedia_type() == 'image'
        ? "<img src=\"{$this->media->getURL(THUMBNAIL_SIZE)}\" />"
        : $this->media->getInternalFilename();

echo "
<div class=\"thumbnail\">
	<div class=\"image\">
		<a href=\"{$this->media->getURL()}\">$img</a>
	</div>
	<div class=\"date\">
		{$this->media->getUploaded(DATE_FORMAT)}
		$deleteButton
	</div>
</div>
";
