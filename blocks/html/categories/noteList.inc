<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Category $this->category
 */
$type = View::escape($this->category->getName());
$addButton = $this->template->linkButton(
	'Add',BASE_URL.'/categories/updateNote.php?category_id='.$this->category->getId(),'add'
);
echo "
<h1>$type</h1>
<h2>$addButton Pre-defined Notes</h2>
";
?>
<table>
<?php
	foreach ($this->category->getNotes() as $note) {
		$editButton = $this->template->linkButton(
			'Edit',
			BASE_URL.'/categories/updateNote.php?note_id='.$note->getId(),
			'edit'
		);
		$deleteButton = $this->template->linkButton(
			'Delete',
			BASE_URL.'/categories/deleteNote.php?note_id='.$note->getId(),
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
