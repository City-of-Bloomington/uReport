<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$title = $this->actionType->getId() ? 'Edit Action Type' : 'Add Action Type';
$name = View::escape($this->actionType->getName());
$verb = View::escape($this->actionType->getVerb());
?>
<h1><?php echo $title; ?></h1>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
	<fieldset><legend>Action Type</legend>
		<input name="actionType_id" type="hidden" value="<?php echo $this->actionType->getId(); ?>" />
		<table>

			<tr><td><label for="name" class="required">Name</label></td>
				<td><input name="name" id="name" value="<?php echo $name; ?>" />
				</td>
			</tr>

			<tr><td><label for="verb" class="required">Verb</label></td>
				<td><input name="verb" id="verb" value="<?php echo $verb; ?>" /></td>
			</tr>

		</table>
		<?php
			echo $this->template->formButton('Submit','submit','submit');
			echo $this->template->formButton(
				'Cancel','button','cancel',null,"document.location.href='".BASE_URL."/actions';"
			);
		?>
	</fieldset>
</form>