<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Client $this->client
 */
$name = View::escape($this->client->getName());
$url = $this->client->getURL();
$person_id = $this->client->getContactPerson()
	? $this->client->getContactPerson()->getId()
	: '';

$title = $this->client->getId() ? 'Edit Client' : 'Add Client';
?>
<div class="updateClientForm">
	<h1><?php echo $title; ?></h1>
	<form method="post" action="<?php echo BASE_URL; ?>/clients/updateClient.php">
		<input name="client_id" type="hidden" value="<?php echo $this->client->getId(); ?>" />
		<fieldset><legend>Client Info</legend>
			<table>
				<tr><td><label for="name">Name</label></td>
					<td><input name="name" id="name" value="<?php echo $name; ?>" /></td>
				</tr>
				<tr><td><label for="url">URL</label></td>
					<td><input name="url" id="url" value="<?php echo $url; ?>" /></td>
				</tr>
			</table>
		</fieldset>
		<fieldset><legend>Contact Info</legend>
			<table>
				<tr><td><label for="contactPerson_id">Person ID</label></td>
					<td><input name="contactPerson_id" id="contactPerson_id"
							value="<?php echo $person_id; ?>" />
					</td>
				</tr>
			</table>
		</fieldset>
		<fieldset>
		<?php
			echo $this->template->formButton('Submit','submit','submit');
			echo $this->template->formButton(
				'Cancel','button','cancel',null,"document.location.href='".BASE_URL."/clients';"
			);
		?>
		</fieldset>
	</form>
</div>