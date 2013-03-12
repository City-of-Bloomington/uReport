<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Client $this->client
 */
$name = View::escape($this->client->getName());
$url = $this->client->getURL();
$api_key = View::escape($this->client->getApi_key());

$title = $this->client->getId() ? 'Edit Client' : 'Add Client';
?>
<div class="updateClientForm">
	<h1><?php echo $title; ?></h1>
	<form method="post" action="<?php echo BASE_URL; ?>/clients/update">
		<fieldset><legend>Client Info</legend>
			<input name="client_id" type="hidden" value="<?php echo $this->client->getId(); ?>" />
			<table>
				<tr><td><label for="name">Name</label></td>
					<td><input name="name" id="name" value="<?php echo $name; ?>" /></td>
				</tr>
				<tr><td><label for="url">URL</label></td>
					<td><input name="url" id="url" value="<?php echo $url; ?>" /></td>
				</tr>
				<tr><td><label>API Key</label></td>
					<td><input name="api_key" id="api_key" value="<?php echo $api_key; ?>" /></td>
				</tr>
			</table>
		</fieldset>
		<fieldset><legend>Contact Info</legend>
			<table>
				<tr><td><label for="contactPerson_id">Contact Person</label></td>
					<td><?php
							$contactPerson = $this->client->getContactPerson();
							$helper = $this->template->getHelper('personChooser');
							echo $helper->personChooser('contactPerson', $contactPerson);
						?>
					</td>
				</tr>
			</table>
		</fieldset>
		<fieldset>
			<button type="submit" class="submit">Submit</button>
			<a class="cancel" href="<?php echo BASE_URL; ?>/clients">Cancel</a>
		</fieldset>
	</form>
</div>