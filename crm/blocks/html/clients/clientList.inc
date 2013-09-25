<?php
/**
 * @copyright 2011-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param ClientList $this->clientList
 */
?>
<div class="clientList">
	<h2><?php
			echo $this->translate(array('labels.client', 'labels.clients', 2));
			if (userIsAllowed('clients','update')) {
				echo sprintf(
					'<a class="btn" href="%s"><span class="icon-plus"></span> %s</a>',
					BASE_URI.'/clients/update',
					$this->translate('labels.add')
				);
			}
		?>
	</h2>
	<p><?php echo $this->translate('text.clients_description'); ?>	</p>
	<?php
		foreach ($this->clientList as $client) {
			$editButton = '';
			$deleteButton = '';
			if (userIsAllowed('clients','update')) {
				$editButton = sprintf(
					'<a class="btn" href="%s"><span class="icon-pencil"></span> %s</a>',
					BASE_URI.'/clients/update?client_id='.$client->getId(),
					$this->translate('labels.edit')
				);
				$deleteButton = sprintf(
					'<a class="btn" href="%s"><span class="icon-remove"></span> %s</a>',
					BASE_URI.'/clients/delete?client_id='.$client->getId(),
					$this->translate('labels.delete')
				);
			}
			$name = View::escape($client->getName());
			$url = $client->getURL();
			$contact = $client->getContactPerson();
			$contactName = View::escape($contact->getFullname());
			echo "
			<div>
				<h3>$name $editButton $deleteButton</h3>
				<table>
					<tr><th>{$this->translate(array('labels.contactMethod','labels.contactMethods',1))}</th>
						<td>{$client->getContactMethod()}</td>
					</tr>
					<tr><th>{$this->translate(array('labels.api_key','labels.api_key',1))}</th>
						<td>{$client->getApi_key()}</td>
					</tr>
					<tr><th>{$this->translate('labels.url')}</th>
						<td><a href=\"$url\">$url</a></td>
					</tr>
					<tr><th>{$this->translate(array('labels.contact','labels.contacts',1))}</th>
						<td><a href=\"{$contact->getURL()}\">$contactName</a></td>
					</tr>
				</table>
			</div>
			";
		}
	?>
</div>
