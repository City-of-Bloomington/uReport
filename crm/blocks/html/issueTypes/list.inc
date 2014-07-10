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
			echo $this->translate(array('labels.issueType', 'labels.issueTypes', 2));
			if (Person::isAllowed('issueTypes','update')) {
				echo sprintf('<a class="btn" href="%s"><span class="icon-plus"></span> %s</a>',
					BASE_URI.'/issueTypes/update',
					$this->translate('labels.add')
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
				$editButton = "
				<a class=\"btn\"
					href=\"".BASE_URI."/issueTypes/update?issueType_id={$type->getId()}\">
					<span class=\"icon-pencil\"></span>
					{$this->translate('labels.edit')}
				</a>
				";
			}
			$name = View::escape($type->getName());
			echo "<li>$editButton $name</li>";
		}
	?>
	</ul>
</div>
