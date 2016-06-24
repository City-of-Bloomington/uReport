<?php
/**
 * @copyright 2012-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Application\Models\IssueTypeTable;
use Application\Models\Person;
use Blossom\Classes\View;
?>
<div>
	<h2><?php
			echo $this->_(array('labels.issueType', 'labels.issueTypes', 2));
			if (Person::isAllowed('issueTypes','update')) {
				$helper = $this->template->getHelper('buttonLink');
				echo $helper->buttonLink(
					BASE_URI.'/issueTypes/update',
					$this->_('labels.add'),
					'add'
				);
			}
		?>
	</h2>
	<ul>
	<?php
		$t = new IssueTypeTable();
		$list = $t->find();
 		foreach ($list as $type) {
			if (Person::isAllowed('issueTypes','update')) {
				$editButton = $helper->buttonLink(
					BASE_URI.'/issueTypes/update?issueType_id='.$type->getId(),
					$this->_('labels.edit'),
					'edit'
				);
			}
			$name = self::escape($type->getName());
			echo "<li>$editButton $name</li>";
		}
	?>
	</ul>
</div>
