<?php
/**
 * @copyright 2013-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Issue $this->issue
 * @param boolean $this->disableButtons
 */
use Application\Models\Person;
use Blossom\Classes\Block;

$uploadButton = '';
if (!$this->disableButtons && Person::isAllowed('media','upload')) {
	$uploadButton = "
	<a class=\"btn\"
		href=\"".BASE_URL."/media/upload?issue_id={$this->issue->getId()}\">
		<span class=\"fa fa-upload\"></span>
		{$this->_('upload_media')}
	</a>
	";
}
$this->mediaList = $this->issue->getMedia();
if ($uploadButton || count($this->mediaList)) {
	echo "
	<div class=\"issueAttachments\">
		<h2>{$this->_(['media', 'medias', 2])} $uploadButton</h2>
	";
		$profileImage = $this->issue->getProfileImage();
		if ($profileImage) {
			$thumbnail = new Block('media/thumbnail.inc', ['media'=>$profileImage]);
			echo "
			<div>
				<a href=\"".BASE_URI."/tickets/thumbnails?ticket_id={$this->issue->getTicket_id()}\">
					{$this->_('see_all_thumbnails')}
				</a>
			</div>
			";
			echo $thumbnail->render('html', $this->template);
		}
		$this->_include('media/mediaList.inc');
	echo "</div>";
}
