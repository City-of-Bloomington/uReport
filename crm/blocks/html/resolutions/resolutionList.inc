<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param ResolutionList $this->resolutionList
 */
?>
<div class="resolutionList">
	<h1><?php
			if (userIsAllowed('Resolutions')) {
				echo '<a class="add button" href="'.BASE_URL.'/resolutions/updateResolution.php">Add</a>';
			}
		?>
		Resolutions
	</h1>
	<table>
		<?php
			foreach ($this->resolutionList as $resolution) {
				$editButton = '';
				if (userIsAllowed('Resolutions')) {
					$editButton = "
					<a class=\"edit button\"
						href=\"".BASE_URL."/resolutions/updateResolution.php?resolution_id={$resolution->getId()}\">
						Edit
					</a>
					";
				}
				$name = View::escape($resolution->getName());
				$description = View::escape($resolution->getDescription());
				echo "
				<tr><td>$editButton</td>
					<td>$name</td>
					<td>$description</td>
				</tr>
				";
			}
		?>
	</table>
</div>