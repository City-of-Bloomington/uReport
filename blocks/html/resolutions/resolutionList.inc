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
				echo $this->template->linkButton(
					'Add',BASE_URL.'/resolutions/updateResolution.php','add'
				);
			}
		?>
		Resolutions
	</h1>
	<table>
		<?php
			foreach ($this->resolutionList as $resolution) {
				$editButton = '';
				if (userIsAllowed('Resolutions')) {
					$editButton = $this->template->linkButton(
						'Edit',
						BASE_URL."/resolutions/updateResolution.php?resolution_id={$resolution->getId()}",
						'edit'
					);
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