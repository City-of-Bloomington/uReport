<?php
/**
 * @copyright 2009-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param PersonList $this->personList
 */
?>
<div class="personList interfaceBox">
	<h2><?php echo $this->translate(array('labels.person', 'labels.people', 2)); ?></h2>
	<table>
		<thead>
			<tr><th><?php echo $this->translate('labels.name'); ?></th>
				<th><?php echo $this->translate(array('labels.email'  , 'labels.emails'   , 1)); ?></th>
				<th><?php echo $this->translate(array('labels.address', 'labels.addresses', 1)); ?></th>
				<th><?php echo $this->translate(array('labels.phone'  , 'labels.phones'   , 1)); ?></th>
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
				$organization = View::escape($person->getOrganization());

				$emailList = '';
				$emails = $person->getEmails();
				if (count($emails)) {
					$list = '<ul>';
					foreach ($emails as $e) {
						$email = View::escape($e->getEmail());
						$list.= "<li>$email</li>";
					}
					$list.= '</ul>';
					$emailList = $list;
				}

				$addressList = '';
				$addresses = $person->getAddresses();
				if (count($addresses)) {
					$list = '<ul>';
					foreach ($addresses as $a) {
						$address = View::escape($a->getAddress());
						$list.= "<li>$address</li>";
					}
					$list.= '</ul>';
					$addressList = $list;
				}

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
					<td>$emailList</td>
					<td>$addressList</td>
					<td>$phoneList</td>
				</tr>
				";
			}
		?>
		</tbody>
	</table>
</div>
