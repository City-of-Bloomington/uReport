<?php
/**
 * @copyright 2012-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
?>
<div>
	<h2><?php
			echo $this->translate(array('labels.contactMethod','labels.contactMethods',2));
			if (userIsAllowed('contactMethods','update')) {
				echo sprintf('<a class="btn" href="%s"><span class="icon-plus"></span> %s</a>',
					BASE_URI.'/contactMethods/update',
					$this->translate('labels.add')
				);
			}
		?>
	</h2>
	<p><?php echo $this->translate('text.contactMethods_description'); ?></p>
	<ul>
	<?php
		$list = new ContactMethodList();
		$list->find();
 		foreach ($list as $method) {
			if (userIsAllowed('contactMethods','update')) {
				$editButton = sprintf('<a class="btn" href="%s"><span class="icon-pencil"></span> %s</a>',
					BASE_URI.'/contactMethods/update?contactMethod_id='.$method->getId(),
					$this->translate('labels.edit')
				);
			}
			$name = View::escape($method->getName());
			echo "<li>$editButton $name</li>";
		}
	?>
	</ul>
</div>
