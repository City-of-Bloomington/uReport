<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param ClientList $this->clientList
 */
?>
<div class="clientList">
	<h2>Clients
		<?php
			if (userIsAllowed('clients','update')) {
				echo '<a class="btn" href="'.BASE_URL.'/clients/update"><span class="icon-plus"></span> Add</a>';
			}
		?>
	</h2>
	<p>These are authorized clients that are allowed to POST tickets via web services.
		The only web service interface we have right now is Open311.
	</p>
	<?php
		foreach ($this->clientList as $client) {
			$editButton = '';
			$deleteButton = '';
			if (userIsAllowed('clients','update')) {
				$editButton = "
				<a class=\"btn\"
					href=\"".BASE_URL."/clients/update?client_id={$client->getId()}\">
					<span class=\"icon-pencil\"></span>
					Edit
				</a>
				";
				$deleteButton = "
				<a class=\"btn\"
					href=\"".BASE_URL."/clients/delete?client_id={$client->getId()}\">
					<span class=\"icon-remove\"></span>
					Delete
				</a>
				";
			}
			$name = View::escape($client->getName());
			$url = $client->getURL();
			$contact = $client->getContactPerson();
			$contactName = View::escape($contact->getFullname());
			echo "
			<div>
				<h3>$name $editButton $deleteButton</h3>
				<table>
					<tr><th>contactMethod</th><td>{$client->getContactMethod()}</td></tr>
					<tr><th>api_key</th><td>{$client->getApi_key()}</td></tr>
					<tr><th>url</th><td><a href=\"$url\">$url</a></td></tr>
					<tr><th>contact</th><td><a href=\"{$contact->getURL()}\">$contactName</a></td></tr>
				</table>
			</div>
			";
		}
	?>
</div>
