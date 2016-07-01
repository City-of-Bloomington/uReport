<?php
/**
 * @copyright 2012-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Application\Models\ContactMethodTable;
use Application\Models\Person;

$table = new ContactMethodTable();
$list = $table->find();
?>
<section>
    <header>
        <h1><?= $this->_(['contactMethod', 'contactMethods', count($list)]); ?></h1>
        <div class="tools">
        <?php
			if (Person::isAllowed('contactMethods','update')) {
				$helper = $this->template->getHelper('buttonLink');
				echo $helper->buttonLink(
					BASE_URI.'/contactMethods/update',
					$this->_('add'),
					'add'
				);
			}
		?>
		</div>
	</header>
	<p><?= $this->_('contactMethods_description', 'messages'); ?></p>
	<ul>
	<?php
 		foreach ($list as $method) {
			if (Person::isAllowed('contactMethods','update')) {
				$editButton = $helper->buttonLink(
					BASE_URI.'/contactMethods/update?contactMethod_id='.$method->getId(),
					$this->_('edit'),
					'edit'
				);
			}
			$name = self::escape($method->getName());
			echo "<li>$editButton $name</li>";
		}
	?>
	</ul>
</div>
