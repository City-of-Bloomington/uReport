<?php
/**
 * @copyright 2012-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
?>
<div>
	<h2><?php
			echo $this->translate(array('labels.issueType', 'labels.issueTypes', 2));
			if (userIsAllowed('issueTypes','update')) {
				echo sprintf('<a class="btn" href="%s"><span class="icon-plus"></span> %s</a>',
					BASE_URI.'/issueTypes/update',
					$this->translate('labels.add')
				);
			}
		?>
	</h2>
	<ul>
	<?php
		$list = new IssueTypeList();
		$list->find();
 		foreach ($list as $type) {
			if (userIsAllowed('issueTypes','update')) {
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
