<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Label $this->label
 */
$title = $this->label->getId() ? 'Edit Label' : 'Add Label';
$name = View::escape($this->label->getName());
?>
<div class="updateLabelForm">
	<h1><?php echo $title; ?></h1>
	<form method="post" action="<?php echo BASE_URL; ?>/labels/updateLabel.php">
		<fieldset>
			<input name="label_id" type="hidden" value="<?php echo $this->label->getId(); ?>" />
			<table>
				<tr><td><label for="name">Name</label></td>
					<td><input name="name" id="name" value="<?php echo $name; ?>" /></td>
				</tr>
			</table>
			<?php
				echo $this->template->formButton('Submit','submit','submit');
				echo $this->template->formButton(
					'Cancel','button','cancel',null,"document.location.href='".BASE_URL."/labels';"
				);
			?>
		</fieldset>
	</form>
</div>