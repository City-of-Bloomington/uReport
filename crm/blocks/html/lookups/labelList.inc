<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
?>
<div class="labelList">
	<h1>Labels
		<?php
			if (userIsAllowed('Lookups')) {
				echo '<a class="edit button" href="'.BASE_URL.'/lookups/updateLabels.php">Edit</a>';
			}
		?>
	</h1>
	<p>These are the predefined labels tha users will be able to use on issues.</p>
	<ul>
	<?php
		foreach (Lookups::get('labels') as $label) {
			$label = View::escape($label);
			echo "<li>$label</li>";
		}
	?>
	</ul>
</div>