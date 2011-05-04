<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Category $this->category
 * @param int $this->index
 * @param string $this->problem
 */
$category = View::escape($this->category);
$title = $this->index ? 'Edit Problem' : 'Add Problem';
echo "
<h1>$category</h1>
<h2>$title</h2>
";
?>
<form method="post" action="<?php echo BASE_URL; ?>/categories/updateProblem.php">
	<fieldset>
		<input name="index" type="hidden" value="<?php echo $this->index; ?>" />
		<input name="category_id" type="hidden" value="<?php echo $this->category->getId(); ?>" />

		<table>
			<tr><td><label for="problem" class="required">Note</label></td>
				<td><input name="problem" id="problem"
						value="<?php echo View::escape($this->problem); ?>" />
				</td>
			</tr>
		</table>

		<?php
			echo $this->template->formButton('Submit','submit','submit');
			echo $this->template->formButton(
				'Cancel','button','cancel',null,
				"document.location.href='".BASE_URL."/categories/problems.php?category_id={$this->category->getid()}'"
			);
		?>
	</fieldset>
</form>
