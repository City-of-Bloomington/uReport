<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$title = $this->contactMethod->getId() ? 'Edit ContactMethod' : 'Add ContactMethod';
?>
<h1><?php echo $title; ?></h1>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
	<fieldset><legend>ContactMethod Info</legend>
		<input name="id" type="hidden" value="<?php echo $this->contactMethod->getId(); ?>" />
		<table>

			<tr><td><label for="name">name</label></td>
				<td><input name="name" id="name" value="<?php echo $this->contactMethod->getName(); ?>" />
				</td>
			</tr>

		</table>
		<?php
			echo $this->template->formButton('Submit','submit','submit');
			echo $this->template->formButton(
				'Cancel','button','cancel',null,"document.location.href='".BASE_URL."/contactMethods';"
			);
		?>
	</fieldset>
</form>