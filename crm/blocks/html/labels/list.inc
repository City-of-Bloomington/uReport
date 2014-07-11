<?php
/**
 * @copyright 2013-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Application\Models\LabelTable;
use Application\Models\Person;
use Blossom\Classes\View;
?>
<div>
	<h2><?php
			echo $this->translate(array('labels.label', 'labels.labels', 2));
			if (Person::isAllowed('labels','update')) {
				$helper = $this->template->getHelper('buttonLink');
				echo $helper->buttonLink(
					BASE_URI.'/labels/update',
					$this->translate('labels.add'),
					'add'
				);
			}
		?>
	</h2>
	<p><?php echo $this->translate('text.labels_description'); ?></p>
	<ul>
	<?php
		$t = new LabelTable();
		$list = $t->find();
 		foreach ($list as $label) {
			if (Person::isAllowed('labels','update')) {
				$editButton = $helper->buttonLink(
					BASE_URI.'/labels/update?label_id='.$label->getId(),
					$this->translate('labels.edit'),
					'edit'
				);
			}
			$name = View::escape($label->getName());
			echo "<li>$editButton $name</li>";
		}
	?>
	</ul>
</div>
