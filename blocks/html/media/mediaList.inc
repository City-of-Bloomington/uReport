<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Issue $this->issue
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
				echo "
				<tr><td><a href=\"{$media->getURL()}\" class=\"{$media->getExtension()}\">
							{$media->getFilename()}
						</a>
					</td>
					<td></td>
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