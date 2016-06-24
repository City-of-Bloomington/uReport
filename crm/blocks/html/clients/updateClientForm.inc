<?php
/**
 * @copyright 2011-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Client $this->client
 */
use Application\Models\ContactMethodTable;
use Blossom\Classes\View;

$name = self::escape($this->client->getName());
$url = $this->client->getURL();
$api_key = self::escape($this->client->getApi_key());

$title = $this->client->getId()
	? $this->_('edit').' '.$this->_(array('client','clients',1))
	: $this->_('add' ).' '.$this->_(array('client','clients',1));
?>
<div class="updateClientForm">
	<h1><?= $title; ?></h1>
	<form method="post" action="<?= BASE_URL; ?>/clients/update">
		<fieldset>
			<legend>
				<?php
					echo $this->_(array('client','clients',1)).' '.$this->_('info');
				?>
			</legend>
			<input name="client_id" type="hidden" value="<?= $this->client->getId(); ?>" />
			<table>
				<tr><td><label for="name"><?= $this->_('name'); ?></label></td>
					<td><input name="name" id="name" value="<?= $name; ?>" /></td>
				</tr>
				<tr><td><label for="url"><?= $this->_('url'); ?></label></td>
					<td><input name="url" id="url" value="<?= $url; ?>" /></td>
				</tr>
				<tr><td><label><?= $this->_('api_key'); ?></label></td>
					<td><input name="api_key" id="api_key" value="<?= $api_key; ?>" /></td>
				</tr>
				<tr><td><label><?= $this->_(array('contactMethod','contactMethods',1)); ?></label></td>
					<td><select name="contactMethod_id" id="contactMethod_id">
						<?php
							$t = new ContactMethodTable();
							$contactMethods = $t->find();
							foreach ($contactMethods as $method) {
								$selected = $this->client->getContactMethod_id()==$method->getId()
									? 'selected="selected"'
									: '';
								$name = self::escape($method->getName());
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
					echo $this->_('contact').' '.$this->_('info');
				?>
			</legend>
			<table>
				<tr><td><label for="contactPerson_id"><?= $this->_('contactPerson'); ?></label></td>
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
