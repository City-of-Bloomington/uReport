<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Ticket $this->ticket_id
 * @param int $this->index  The index number of the issue
 */
$return_url = $_SERVER['REQUEST_URI'];
?>
<div class="uploadForm">
	<form method="post" action="<?php echo BASE_URL; ?>/media/upload.php" enctype="multipart/form-data">
		<fieldset><legend>Upload an attachment</legend>
			<input name="ticket_id" type="hidden" value="<?php echo $this->ticket->getId(); ?>" />
			<input name="index" type="hidden" value="<?php echo $this->index; ?>" />
			<input name="return_url" type="hidden" value="<?php echo $return_url; ?>" />

			<input type="file" name="attachment" />
			<?php
				echo $this->template->formButton('Submit','submit','submit');
				echo $this->template->formButton('Cancel','reset','cancel');
			?>
		</fieldset>
	</form>
</div>