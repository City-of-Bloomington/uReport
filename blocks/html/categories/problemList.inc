<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Category $this->category
 */
$type = View::escape($this->category->getName());
$addButton = $this->template->linkButton(
	'Add',BASE_URL.'/categories/updateProblem.php?category_id='.$this->category->getId(),'add'
);
echo "
<h1>$type</h1>
<h2>$addButton Pre-defined Problems</h2>
";
?>
<table>
<?php
	foreach ($this->category->getProblems() as $index=>$problem) {
		$editButton = $this->template->linkButton(
			'Edit',
			BASE_URL."/categories/updateProblem.php?category_id={$this->category->getId()};index=$index",
			'edit'
		);
		$deleteButton = $this->template->linkButton(
			'Delete',
			BASE_URL."/categories/deleteProblem.php?category_id={$this->category->getId()};index=$index",
			'delete'
		);
		$text = View::escape($problem);
		echo "
		<tr><td>$editButton $deleteButton</td>
			<td>$text</td>
		</tr>
		";
	}
?>
</table>
