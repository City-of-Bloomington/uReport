<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
?>
<div>
	<h2>Labels
		<?php
			if (userIsAllowed('labels','update')) {
				echo '<a class="add button" href="'.BASE_URL.'/labels/update">Add</a>';
			}
		?>
	</h2>
	<p>These are the predefined labels tha users will be able to use on issues.</p>
	<ul>
	<?php
		$list = new LabelList();
		$list->find();
 		foreach ($list as $label) {
			if (userIsAllowed('labels','update')) {
				$editButton = "
				<a class=\"edit button\"
					href=\"".BASE_URL."/labels/update?label_id={$label->getId()}\">
					Edit
				</a>
				";
			}
			$name = View::escape($label->getName());
			echo "<li>$editButton $name</li>";
		}
	?>
	</ul>
</div>