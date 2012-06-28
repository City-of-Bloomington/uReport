<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Issue $this->issue
 */
?>
<div class="uploadForm">
	<form method="post" action="<?php echo BASE_URL; ?>/media/upload" enctype="multipart/form-data">
		<fieldset><legend>Upload an attachment</legend>
			<input name="issue_id"   type="hidden" value="<?php echo $this->issue->getId(); ?>" />
			<input name="attachment" type="file" />
			<button type="submit" class="submit">Upload</button>
			<a class="cancel" href="<?php echo $this->issue->getTicket()->getURL(); ?>">Cancel</a>
		</fieldset>
	</form>
</div>
