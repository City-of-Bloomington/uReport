<?php
/**
 * HTML for displaying a thumbnail
 *
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Media $this->media
 */
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;

$deleteButton = '';
if (!$this->disableButtons && Person::isAllowed('issues','update')) {
	$h = $this->template->getHelper('buttonLink');
	$deleteButton = $h->buttonLink(
		BASE_URI.'/media/delete?media_id='.$this->media->getId(),
		$this->translate('labels.delete'),
		'delete',
		ButtonLink::SIZE_ICON
	);
}
echo "
<div class=\"thumbnail\">
	<div class=\"image\">
		<a href=\"{$this->media->getURL()}\">
			<img src=\"{$this->media->getURL(THUMBNAIL_SIZE)}\" />
		</a>
	</div>
	<div class=\"date\">
		{$this->media->getUploaded(DATE_FORMAT)}
		$deleteButton
	</div>
</div>
";
