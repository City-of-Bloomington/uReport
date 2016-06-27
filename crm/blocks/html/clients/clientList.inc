<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param ClientList $this->clientList
 */
use Application\Models\Person;
?>
<div class="clientList">
	<h2><?php
			echo $this->_(array('client', 'clients', 2));
			if (Person::isAllowed('clients','update')) {
				$helper = $this->template->getHelper('buttonLink');
				echo $helper->buttonLink(
					BASE_URI.'/clients/update',
					$this->_('add'),
					'add'
				);
			}
		?>
	</h2>
	<p><?= $this->_('clients_description', 'messages'); ?></p>
	<?php
		foreach ($this->clientList as $client) {
			$editButton   = '';
			$deleteButton = '';
			if (Person::isAllowed('clients','update')) {
				$editButton = $helper->buttonLink(
					BASE_URI.'/clients/update?client_id='.$client->getId(),
					$this->_('edit'),
					'edit'
				);
				$deleteButton = $helper->buttonLink(
					BASE_URI.'/clients/delete?client_id='.$client->getId(),
					$this->_('delete'),
					'delete'
				);
			}
			$url         = $client->getURL();
			$contact     = $client->getContactPerson();
			$name        = self::escape($client->getName());
			$contactName = self::escape($contact->getFullname());
			echo "
			<div>
				<h3>$name $editButton $deleteButton</h3>
				<table>
					<tr><th>{$this->_(array('contactMethod','contactMethods',1))}</th>
						<td>{$client->getContactMethod()}</td>
					</tr>
					<tr><th>{$this->_('api_key')}</th>
						<td>{$client->getApi_key()}</td>
					</tr>
					<tr><th>{$this->_('url')}</th>
						<td><a href=\"$url\">$url</a></td>
					</tr>
					<tr><th>{$this->_('contact')}</th>
						<td><a href=\"{$contact->getURL()}\">$contactName</a></td>
					</tr>
				</table>
			</div>
			";
		}
	?>
</div>
