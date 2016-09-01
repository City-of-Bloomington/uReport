<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param ClientList $this->clientList
 */
use Application\Models\Person;
?>
<section class="clientList">
    <header>
        <h1><?= $this->template->title; ?></h1>
        <div class="tools">
        <?php
			if (Person::isAllowed('clients','update')) {
				$helper = $this->template->getHelper('buttonLink');
				echo $helper->buttonLink(
					BASE_URI.'/clients/update',
					$this->_('add'),
					'add'
				);
			}
		?>
		</div>
    </header>
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
			<article>
                <header>
                    <h1>$name</h1>
                    <div class=\"tools\">
                        $editButton $deleteButton
                    </div>
                </header>

                <dl><dt>{$this->_('contactMethod')}</dt>
                    <dd>{$client->getContactMethod()}</dd>
                </dl>
                <dl><dt>{$this->_('api_key')}</dt>
                    <dd>{$client->getApi_key()}</dd>
                </dl>
                <dl><dt>{$this->_('url')}</dt>
                    <dd><a href=\"$url\">$url</a></dd>
                </dl>
                <dl><dt>{$this->_('contact')}</dt>
                    <dd><a href=\"{$contact->getURL()}\">$contactName</a></dd>
                </dl>
			</article>
			";
		}
	?>
</section>
