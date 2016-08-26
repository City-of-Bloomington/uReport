<?php
/**
 * @copyright 2009-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param PersonList $this->personList
 */
use Blossom\Classes\Url;

$callback = !empty($_REQUEST['callback'])
    ? preg_replace('/[^a-zA-Z_\.]/', '', $_REQUEST['callback'])
    : '';
?>
<div class="personList">
	<h2><?= $this->_(['person', 'people', count($this->personList)]); ?></h2>

	<table>
		<thead>
			<tr><?= $callback ? '<th></th>' : ''; ?>
                <th><?= $this->_('name'   ); ?></th>
				<th><?= $this->_('email'  ); ?></th>
				<th><?= $this->_('address'); ?></th>
				<th><?= $this->_('phone'  ); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php

			foreach ($this->personList as $person) {
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

                $person_id = $person->getId();
				if ($callback) {
					$this->template->addToAsset('scripts', JQUERY.'/jquery.min.js');
					$this->template->addToAsset('scripts', BASE_URI.'/js/issues/changePerson.js');

					$personLink = "
					<td><button type=\"button\" onclick=\"self.opener.$callback('$person_id');\">
                            {$this->_('use_person')}
                        </button>
                    </td>
                    <td>$name</td>
					";
				}
				else {
                    if (isset($_REQUEST['return_url'])) {
                        $url = new Url($_REQUEST['return_url']);
                        $url->person_id = $person_id;

                        foreach ($_REQUEST as $key=>$value) {
                            if ($value && $key!='ticket_id' && $key!='return_url') {
                                $url->$key = $value;
                            }
                        }
                    }
                    else {
                        $url = $person->getURL();
                    }
                    $personLink = "<td><a href=\"$url\">$name</a></td>";
				}

				echo "
				<tr draggable=\"true\" ondragstart=\"event.dataTransfer.setData('text/plain', '$person_id');\">
                    $personLink
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
