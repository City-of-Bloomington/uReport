<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
?>
<div class="interfaceBox">
	<h1><?php
			if (userIsAllowed('IssueTypes')) {
				echo $this->template->linkButton(
					'Add',BASE_URL.'/issueTypes/updateIssueType.php','add'
				);
			}
		?>
		Issue Types
	</h1>
	<ul><?php
			foreach ($this->issueTypeList as $issueType) {
				$editButton = '';
				if (userIsAllowed('IssueTypes')) {
					$url = new URL(BASE_URL.'/issueTypes/updateIssueType.php');
					$url->id = $issueType->getId();

					$editButton = $this->template->linkButton(
						'Edit',
						BASE_URL.'/issueTypes/updateIssueType.php?id='.$issueType->getId(),
						'edit'
					);
				}
				echo "<li>$editButton $issueType</li>";
			}
		?>
	</ul>
</div>