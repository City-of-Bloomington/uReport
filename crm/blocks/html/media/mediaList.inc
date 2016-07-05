<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param MediaList $this->mediaList
 * @param bool $this->disableButtons
 */
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;

if (count($this->mediaList)) {
    $userCanDelete = Person::isAllowed('media', 'delete');
    $h             = $this->template->getHelper('buttonLink');

	echo "<table class=\"mediaList\">";
	foreach ($this->mediaList as $media) {
		$deleteButton = '';
		if (!$this->disableButtons && $userCanDelete) {
			$deleteButton = $h->buttonLink(
				BASE_URI.'/media/delete?media_id='.$media->getId(),
				$this->_('delete'),
				'delete',
				ButtonLink::SIZE_ICON
			);
		}
		echo "
		<tr><td><a href=\"{$media->getURL()}\" class=\"{$media->getExtension()}\">
					{$media->getFilename()}
				</a>
			</td>
			<td>{$media->getUploaded(DATE_FORMAT)}</td>
			<td>$deleteButton</td>
		</tr>
		";
	}
	echo "</table>";
}
