<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
?>
<div class="interfaceBox">
	<h1>
		<?php
			if (userIsAllowed('NeighborhoodAssociations')) {
				echo $this->template->linkButton(
					'Add',BASE_URL.'/neighborhoodAssociations/updateNeighborhoodAssociation.php','add'
				);
			}
		?>
		Neighborhood Associations
	</h1>
	<ul><?php
			foreach ($this->neighborhoodAssociationList as $neighborhoodAssociation) {
				$editButton = '';
				if (userIsAllowed('NeighborhoodAssociations')) {
					$url = new URL(BASE_URL.'/neighborhoodAssociations/updateNeighborhoodAssociation.php');
					$url->id = $neighborhoodAssociation->getId();

					$editButton = $this->template->linkButton(
						'Edit',
						BASE_URL.'/neighborhoodAssociations/updateNeighborhoodAssociation.php?id='.$neighborhoodAssociation->getId(),
						'edit'
					);
				}
				$name = View::escape($neighborhoodAssociation->getName());
				echo "<li>$editButton $name</li>";
			}
		?>
	</ul>
</div>