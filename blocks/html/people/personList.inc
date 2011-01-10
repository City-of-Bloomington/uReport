<?php
/**
 * @copyright 2009-2010 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param PersonList $this->personList
 */
?>
<div class="interfaceBox">
	<h1><?php
			if (userIsAllowed('Users')) {
				echo $this->linkButton(
					'Add a Person',BASE_URL.'/people/addPerson.php','add'
				);
			}
		?>
		People
	</h1>
	<table>
		<thead>
			<tr><th></th>
				<th>Username</th>
				<th>Name</th>
				<th>Email</th>
			</tr>
		</thead>
		<tbody>
		<?php
			foreach ($this->personList as $person) {
				$editButton = '';

				if (userIsAllowed('Users')) {
					$editButton = $this->linkButton(
						'Edit',
						BASE_URL.'/people/updatePerson.php?person_id='.$person->getId(),
						'edit'
					);
				}

				$name = View::escape($person->getFullname());
				echo "
				<tr><td>$editButton</td>
					<td>{$person->getUsername()}</td>
					<td><a href=\"{$person->getURL()}\">$name</a></td>
					<td>{$person->getEmail()}</td>
				</tr>
				";
			}
		?>
		</tbody>
	</table>
</div>
