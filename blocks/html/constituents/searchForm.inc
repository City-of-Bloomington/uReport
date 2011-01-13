<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$fields = array('firstname','lastname','email','address','phone');
foreach ($fields as $field) {
	$$field = isset($_GET[$field]) ? View::escape($_GET[$field]) : '';
}
?>
<h1>Find a Constituent</h1>
<form method="get" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
	<fieldset>
		<table>
			<thead>
				<tr><td><label for="firstname">First Name</label></td>
					<td><label for="lastname">Last Name</label></td>
					<td><label for="email">Email</label></td>
					<td><label for="address">Address</label></td>
				</tr>
			</thead>
			<tbody>
				<tr><td><input name="firstname" id="firstname" value="<?php echo $firstname; ?>" /></td>
					<td><input name="lastname" id="lastname" value="<?php echo $lastname; ?>" /></td>
					<td><input name="email" id="email" value="<?php echo $email; ?>" /></td>
					<td><input name="address" id="address" value="<?php echo $address; ?>" /></td>
				</tr>
			</tbody>
		</table>
		<?php
			echo $this->template->formButton(
				'Search','submit','search'
			);
		?>
	</fieldset>
</form>
