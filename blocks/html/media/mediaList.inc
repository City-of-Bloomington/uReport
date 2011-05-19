<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Ticket $this->ticket
 * @param int $this->index
 * @param MediaList $this->mediaList
 * @param bool $this->disableButtons
 */
?>
<div class="mediaList">
	<?php
		if (count($this->mediaList)) {
			echo "
			<table>
			";
			foreach ($this->mediaList as $media) {
				$deleteButton = '';
				if (!$this->disableButtons) {
					$deleteButton = $this->template->linkButton(
						'Delete Attachment',
						BASE_URL.'/media/delete.php?media_id='.$media->getId(),
						'delete'
					);
				}
				echo "
				<tr><td><a href=\"{$media->getURL()}\" class=\"{$media->getExtension()}\">
							{$media->getFilename()}
						</a>
						$deleteButton
					</td>
					<td>{$media->getUploaded()}</td>
				</tr>
				";
			}
			echo "
			</table>
			";
		}

		if (!$this->disableButtons && userIsAllowed('Issues')) {
			include APPLICATION_HOME.'/blocks/html/media/uploadForm.inc';
		}
	?>
</div>