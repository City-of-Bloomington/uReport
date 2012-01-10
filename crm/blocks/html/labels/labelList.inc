<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param LabelList $this->labels
 */
?>
<div class="labelList">
	<h1>Labels
		<?php
			if (userIsAllowed('Labels')) {
				echo $this->template->linkButton(
					'Add',BASE_URL.'/labels/updateLabel.php','add'
				);
			}
		?>
	</h1>
	<p>These are the predefined labels tha users will be able to use on tickets.</p>
	<ul>
	<?php
		foreach ($this->labels as $label) {
			if (userIsAllowed('Labels')) {
				$editButton = $this->template->linkButton(
					'Edit',
					BASE_URL."/labels/updateLabel.php?label_id={$label->getId()}",
					'edit'
				);
				$deleteButton = $this->template->linkButton(
					'Delete',
					BASE_URL."/labels/deleteLabel.php?label_id={$label->getId()}",
					'delete'
				);
			}
			$name = View::escape($label->getName());
			echo "
			<li>$editButton $deleteButton $name</li>
			";
		}
	?>
	</ul>
</div>