<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param IssueType $this->issueType
 */
$type = View::escape($this->issueType->getName());
$addButton = $this->template->linkButton(
	'Add',BASE_URL.'/issueTypes/updateNote.php?issueType_id='.$this->issueType->getId(),'add'
);
echo "
<h1>$type</h1>
<h2>$addButton Pre-defined Notes</h2>
";
?>
<table>
<?php
	foreach ($this->issueType->getNotes() as $note) {
		$editButton = $this->template->linkButton(
			'Edit',
			BASE_URL.'/issueTypes/updateNote.php?note_id='.$note->getId(),
			'edit'
		);
		$deleteButton = $this->template->linkButton(
			'Delete',
			BASE_URL.'/issueTypes/deleteNote.php?note_id='.$note->getId(),
			'delete'
		);
		$text = View::escape($note);
		echo "
		<tr><td>$editButton $deleteButton</td>
			<td>$text</td>
		</tr>
		";
	}
?>
</table>
