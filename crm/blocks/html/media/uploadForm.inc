<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Ticket $this->ticket
 * @param int $this->index  The index number of the issue
 */
?>
<div class="uploadForm">
	<form method="post" action="<?php echo BASE_URL; ?>/media/upload" enctype="multipart/form-data">
		<fieldset><legend>Upload an attachment</legend>
			<input name="ticket_id" type="hidden" value="<?php echo $this->ticket->getId(); ?>" />
			<input name="index" type="hidden" value="<?php echo $this->index; ?>" />

			<input type="file" name="attachment" />
			<button type="submit" class="submit">Upload</button>
			<a href="<?php echo $this->ticket->getURL(); ?>">Cancel</a>
		</fieldset>
	</form>
</div>
