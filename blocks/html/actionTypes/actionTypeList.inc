<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
?>
<div class="interfaceBox">
	<h1><?php
			if (userIsAllowed('ActionTypes')) {
				echo $this->template->linkButton(
					'Add',BASE_URL.'/actionTypes/updateActionType.php','add'
				);
			}
		?>
		Action Types
	</h1>
	<ul><?php
			foreach ($this->actionTypeList as $actionType) {
				$editButton = '';
				if (userIsAllowed('ActionTypes')) {
					$url = new URL(BASE_URL.'/actionTypes/updateActionType.php');
					$url->id = $actionType->getId();

					$editButton = $this->template->linkButton(
						'Edit',
						BASE_URL.'/actionTypes/updateActionType.php?id='.$actionType->getId(),
						'edit'
					);
				}
				$type = View::escape($actionType->getName());
				echo "<li>$editButton $type</li>";
			}
		?>
	</ul>
</div>