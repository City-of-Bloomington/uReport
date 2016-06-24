<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param PersonList $this->personList
 */
use Blossom\Classes\View;
use Blossom\Classes\Url;
?>
<div class="personList interfaceBox">
	<h2><?= $this->_(array('person', 'people', 2)); ?></h2>
	<table>
		<thead>
			<tr><th><?= $this->_('name'); ?></th>
				<th><?= $this->_(array('email'  , 'emails'   , 1)); ?></th>
				<th><?= $this->_(array('address', 'addresses', 1)); ?></th>
				<th><?= $this->_(array('phone'  , 'phones'   , 1)); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
			foreach ($this->personList as $person) {
				if (isset($_REQUEST['return_url'])) {
					$url = new Url($_REQUEST['return_url']);
					$url->person_id = "{$person->getId()}";
					foreach ($_REQUEST as $key=>$value) {
						if ($value && $key!='ticket_id' && $key!='return_url') {
							$url->$key = $value;
						}
					}
				}
				elseif (isset($_REQUEST['callback'])) {
					$this->template->addToAsset('scripts', JQUERY.'/jquery.min.js');
					$this->template->addToAsset('scripts', BASE_URI.'/js/issues/changePerson.js');

					$callback = preg_replace('/[^a-zA-Z_\.]/', '', $_REQUEST['callback']);
					$url = "javascript:self.opener.$callback('{$person->getId()}');";
				}
				else {
					$url = $person->getURL();
				}

				$name         = self::escape($person->getFullname());
				$organization = self::escape($person->getOrganization());

				$emailList = '';
				$emails = $person->getEmails();
				if (count($emails)) {
					$list = '<ul>';
					foreach ($emails as $e) {
						$email = self::escape($e->getEmail());
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
						$address = self::escape($a->getAddress());
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
						$number = self::escape($p->getNumber());
						$list.= "<li>$number</li>";
					}
					$list.= '</ul>';
					$phoneList = $list;
				}
				echo "
				<tr draggable=\"true\" ondragstart=\"event.dataTransfer.setData('text/plain', '{$person->getId()}');\">
                    <td><a href=\"$url\">$name</a></td>
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
