<?php
/**
 * @copyright 2009-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param PersonList $this->personList
 */
?>
<div class="interfaceBox">
	<h1>People</h1>
	<table>
		<thead>
			<tr><th>Name</th>
				<th>Email</th>
				<th>Address</th>
				<th>Phone</th>
				<th>Organization</th>
			</tr>
		</thead>
		<tbody>
		<?php
			foreach ($this->personList as $person) {
				if (isset($_REQUEST['return_url'])) {
					$url = new URL($_REQUEST['return_url']);
					$url->person_id = "{$person->getId()}";
					foreach ($_REQUEST as $key=>$value) {
						if ($value && $key!='ticket_id' && $key!='return_url') {
							$url->$key = $value;
						}
					}
				}
				elseif (isset($_REQUEST['callback'])) {
					$this->template->addToAsset('scripts',YUI.'/yui/yui-min.js');
					$this->template->addToAsset('scripts',BASE_URI.'/js/issues/changePerson.js');

					$callback = preg_replace('/[^a-zA-Z_\.]/', '', $_REQUEST['callback']);
					$url = "javascript:self.opener.$callback('{$person->getId()}');";
				}
				else {
					$url = $person->getURL();
				}

				$name         = View::escape($person->getFullname());
				$address      = View::escape($person->getAddress());
				$phone        = View::escape($person->getPhoneNumber());
				$organization = View::escape($person->getOrganization());
				echo "
				<tr><td><a href=\"$url\">$name</a></td>
					<td><a href=\"$url\">{$person->getEmail()}</a></td>
					<td><a href=\"$url\">$address</a></td>
					<td><a href=\"$url\">$phone</a></td>
					<td><a href=\"$url\">$organization</a></td>
				</tr>
				";
			}
		?>
		</tbody>
	</table>
</div>
