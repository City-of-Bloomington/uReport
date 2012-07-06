<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
?>
<div>
	<h2>Issue Types
		<?php
			if (userIsAllowed('issueTypes','update')) {
				echo '<a class="add button" href="'.BASE_URL.'/issueTypes/update">Add</a>';
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
				<a class=\"edit button\"
					href=\"".BASE_URL."/issueTypes/update?issueType_id={$type->getId()}\">
					Edit
				</a>
				";
			}
			$name = View::escape($type->getName());
			echo "<li>$editButton $name</li>";
		}
	?>
	</ul>
</div>