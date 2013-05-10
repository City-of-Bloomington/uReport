<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Issue $this->issue
 * @param boolean $this->disableButtons
 */
$uploadButton = '';
if (!$this->disableButtons && userIsAllowed('media','upload')) {
	$uploadButton = "
	<a class=\"btn\"
		href=\"".BASE_URL."/media/upload?issue_id={$this->issue->getId()}\">
		<span class=\"icon-upload-alt\"></span>
		Upload Attachment
	</a>
	";
}
$this->mediaList = $this->issue->getMedia();
if ($uploadButton || count($this->mediaList)) {
	echo "
	<div class=\"issueAttachments\">
		<h2>Attachments $uploadButton</h2>
	";
		$profileImage = $this->issue->getProfileImage();
		if ($profileImage) {
			$thumbnail = new Block('media/thumbnail.inc', array('media'=>$profileImage));
			echo "
			<div>
				<a href=\"".BASE_URI."/tickets/thumbnails?ticket_id={$this->issue->getTicket_id()}\">
					See all thumbnails
				</a>
			</div>
			";
			echo $thumbnail->render('html', $this->template);
		}
		include APPLICATION_HOME.'/blocks/html/media/mediaList.inc';
	echo "</div>";
}
