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
			if (userIsAllowed('ContactMethods')) {
				echo $this->template->linkButton(
					'Add',BASE_URL.'/contactMethods/addContactMethod.php','add'
				);
			}
		?>
		Contact Methods
	</h1>
	<ul><?php
			foreach ($this->contactMethodList as $contactMethod) {
				$editButton = '';
				if (userIsAllowed('ContactMethods')) {
					$url = new URL(BASE_URL.'/contactMethods/updateContactMethod.php');
					$url->id = $contactMethod->getId();

					$editButton = $this->template->linkButton(
						'Edit',
						BASE_URL.'/contactMethods/updateContactMethod.php?id='.$contactMethod->getId(),
						'edit'
					);
				}
				$name = View::escape($contactMethod->getName());
				echo "<li>$editButton $name</li>";
			}
		?>
	</ul>
</div>