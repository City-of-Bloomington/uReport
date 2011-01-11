<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param IssueTypeNote $this->note
 */
$type = View::escape($this->note->getIssueType());
$title = $this->note->getId() ? 'Edit Note' : 'Add Note';
echo "
<h1>$type</h1>
<h2>$title</h2>
";
?>
<form method="post" action="<?php echo BASE_URL; ?>/issueTypes/updateNote.php">
	<fieldset>
		<input name="note_id" type="hidden" value="<?php echo $this->note->getId(); ?>" />
		<input name="issueType_id" type="hidden" value="<?php echo $this->note->getIssueType_id(); ?>" />

		<table>
			<tr><td><label for="note" class="required">Note</label></td>
				<td><input name="note" id="note" value="<?php echo View::escape($this->note); ?>" />
				</td>
			</tr>
		</table>

		<?php
			echo $this->template->formButton('Submit','submit','submit');
			echo $this->template->formButton(
				'Cancel','button','cancel',null,
				"document.location.href='".BASE_URL."/issueTypes/notes.php?issueType_id={$this->note->getIssueType_id()}'"
			);
		?>
	</fieldset>
</form>