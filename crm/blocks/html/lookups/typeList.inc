<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
?>
<div class="typeList">
	<h1>Issue Types
		<?php
			if (userIsAllowed('Lookups')) {
				echo $this->template->linkButton(
					'Edit',BASE_URL.'/lookups/updateTypes.php','edit'
				);
			}
		?>
	</h1>
	<p>These are the available issue types.</p>
	<ul>
	<?php
		foreach (Lookups::get('types') as $types) {
			$label = View::escape($types);
			echo "<li>$types</li>";
		}
	?>
	</ul>
</div>