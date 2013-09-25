<?php
/**
 * @copyright 2011-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Client $this->client
 */
$name = View::escape($this->client->getName());
$url = $this->client->getURL();
$api_key = View::escape($this->client->getApi_key());

$title = $this->client->getId()
	? $this->translate('labels.edit').' '.$this->translate(array('labels.client','labels.clients',1))
	: $this->translate('labels.add' ).' '.$this->translate(array('labels.client','labels.clients',1));
?>
<div class="updateClientForm">
	<h1><?php echo $title; ?></h1>
	<form method="post" action="<?php echo BASE_URL; ?>/clients/update">
		<fieldset>
			<legend>
				<?php
					echo $this->translate(array('labels.client','labels.clients',1)).' '.$this->translate('labels.info');
				?>
			</legend>
			<input name="client_id" type="hidden" value="<?php echo $this->client->getId(); ?>" />
			<table>
				<tr><td><label for="name"><?php echo $this->translate('labels.name'); ?></label></td>
					<td><input name="name" id="name" value="<?php echo $name; ?>" /></td>
				</tr>
				<tr><td><label for="url"><?php echo $this->translate('labels.url'); ?></label></td>
					<td><input name="url" id="url" value="<?php echo $url; ?>" /></td>
				</tr>
				<tr><td><label><?php echo $this->translate('labels.api_key'); ?></label></td>
					<td><input name="api_key" id="api_key" value="<?php echo $api_key; ?>" /></td>
				</tr>
				<tr><td><label><?php echo $this->translate(array('labels.contactMethod','labels.contactMethods',1)); ?></label></td>
					<td><select name="contactMethod_id" id="contactMethod_id">
						<?php
							$contactMethods = new ContactMethodList();
							$contactMethods->find();
							foreach ($contactMethods as $method) {
								$selected = $this->client->getContactMethod_id()==$method->getId()
									? 'selected="selected"'
									: '';
								$name = View::escape($method->getName());
								echo "<option value=\"{$method->getId()}\" $selected>$method</option>";
							}
						?>
						</select>
					</td>
				</tr>
			</table>
		</fieldset>
		<fieldset>
			<legend>
				<?php
					echo $this->translate('labels.contact').' '.$this->translate('labels.info');
				?>
			</legend>
			<table>
				<tr><td><label for="contactPerson_id"><?php echo $this->translate('labels.contactPerson'); ?></label></td>
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
			<?php
				$helper = $this->template->getHelper('saveAndCancelButtons');
				echo $helper->saveAndCancelButtons(BASE_URI.'/clients');
			?>
		</fieldset>
	</form>
</div>
