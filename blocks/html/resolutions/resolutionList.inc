<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
?>
<div class="resolutionList interfaceBox">
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
					$url = new URL(BASE_URL.'/resolutions/updateResolution.php');
					$url->id = $resolution->getId();

					$editButton = $this->template->linkButton(
						'Edit',
						BASE_URL.'/resolutions/updateResolution.php?resolution_id='.$resolution->getId(),
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