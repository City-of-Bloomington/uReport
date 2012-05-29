<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Ticket $this->ticket
 * @param int $this->index
 * @param MediaList $this->mediaList
 * @param bool $this->disableButtons
 */
if (count($this->mediaList)) {
	echo "
	<table>
	";
	foreach ($this->mediaList as $media) {
		$deleteButton = '';
		if (!$this->disableButtons) {
			$deleteButton = "
			<a class=\"delete button\" href=\"".BASE_URL."/media/delete?media_id={$media->getId()}\">
				Delete Attachment
			</a>
			";
		}
		echo "
		<tr><td>{$media->getUploaded(DATE_FORMAT)}</td>
			<td><a href=\"{$media->getURL()}\" class=\"{$media->getExtension()}\">
					{$media->getFilename()}
				</a>
				$deleteButton
			</td>
		</tr>
		";
	}
	echo "
	</table>
	";
}
