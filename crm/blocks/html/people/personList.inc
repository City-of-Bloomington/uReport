<?php
/**
 * @copyright 2009-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param PersonList $this->personList
 */
?>
<div class="personList interfaceBox">
	<h2>People</h2>
	<table>
		<thead>
			<tr><th>Name</th>
				<th>Email</th>
				<th>Address</th>
				<th>Phone</th>
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
				$organization = View::escape($person->getOrganization());

				$phoneList = '';
				$phones = $person->getPhones();
				if (count($phones)) {
					$list = '<ul>';
					foreach ($phones as $p) {
						$number = View::escape($p->getNumber());
						$list.= "<li>$number</li>";
					}
					$list.= '</ul>';
					$phoneList = $list;
				}
				echo "
				<tr><td><a href=\"$url\">$name</a></td>
					<td>{$person->getEmail()}</td>
					<td>$address</td>
					<td>$phoneList</td>
				</tr>
				";
			}
		?>
		</tbody>
	</table>
</div>
