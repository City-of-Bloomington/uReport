<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Issue $this->issue
 */
$return_url = $_SERVER['REQUEST_URI'];
?>
<div class="uploadForm">
	<form method="post" action="<?php echo BASE_URL; ?>/media/upload.php" enctype="multipart/form-data">
		<fieldset><legend>Upload an attachment</legend>
			<input name="issue_id" type="hidden" value="<?php echo $this->issue->getId(); ?>" />
			<input name="return_url" type="hidden" value="<?php echo $return_url; ?>" />

			<input type="file" name="attachment" />
			<?php
				echo $this->template->formButton('Submit','submit','submit');
				echo $this->template->formButton('Cancel','reset','cancel');
			?>
		</fieldset>
	</form>
</div>