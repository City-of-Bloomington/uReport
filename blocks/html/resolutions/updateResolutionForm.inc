<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$title = $this->resolution->getId() ? 'Edit Resolution' : 'Add Resolution';
$name = View::escape($this->resolution->getName());
$description = View::escape($this->resolution->getDescription());
?>
<h1><?php echo $title; ?></h1>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
	<fieldset><legend>Resolution Info</legend>
		<input name="resolution_id" type="hidden" value="<?php echo "{$this->resolution->getId()}"; ?>" />
		<table>

			<tr><td><label for="name" class="required">Name</label></td>
				<td><input name="name" id="name" value="<?php echo $name; ?>" /></td>
			</tr>

			<tr><td><label for="description">Description</label></td>
				<td><input name="description" id="description" value="<?php echo $description; ?>" /></td>
			</tr>

		</table>
		<?php
			echo $this->template->formButton('Submit','submit','submit');
			echo $this->template->formButton(
				'Cancel','button','cancel',null,"document.location.href='".BASE_URL."/resolutions';"
			);
		?>
	</fieldset>
</form>