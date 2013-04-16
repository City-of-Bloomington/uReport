<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Issue $this->issue
 */
$uploadButton = '';
if (userIsAllowed('media','upload')) {
	$uploadButton = "
	<a class=\"upload button\"
		href=\"".BASE_URL."/media/upload?issue_id={$this->issue->getId()}\">
		Upload Attachment
	</a>
	";
}
?>
<div class="issueAttachments">
	<h2>Attachments <?php echo $uploadButton; ?></h2>
	<?php
		$profileImage = $this->issue->getProfileImage();
		if ($profileImage) {
			echo "
			<div class=\"profileImage\">
				<a href=\"{$profileImage->getURL()}\">
					<img src=\"{$profileImage->getURL(150)}\" />
				</a>
			</div>
			";
		}
		include APPLICATION_HOME.'/blocks/html/media/mediaList.inc';
	?>
</div>
