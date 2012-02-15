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
	foreach ($this->mediaList as $mediaIndex=>$media) {
		$deleteButton = '';
		if (!$this->disableButtons) {
			$url = new URL(BASE_URL.'/media/delete');
			$url->ticket_id = (string)$this->ticket->getId();
			$url->issueIndex = $this->index;
			$url->mediaIndex = $mediaIndex;
			$deleteButton = "<a class=\"delete button\" href=\"$url\">Delete Attachment</a>";
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
