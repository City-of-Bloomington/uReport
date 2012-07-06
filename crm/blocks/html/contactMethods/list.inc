<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
?>
<div>
	<h2>Contact Methods
		<?php
			if (userIsAllowed('contactMethods','update')) {
				echo '<a class="add button" href="'.BASE_URL.'/contactMethods/update">Add</a>';
			}
		?>
	</h2>
	<p>
		These are the available contact methods for the system.
		They are used both for how the citizen contacted the city and
		for how the citizen would like to be contacted.
	</p>
	<ul>
	<?php
		$list = new ContactMethodList();
		$list->find();
 		foreach ($list as $method) {
			if (userIsAllowed('contactMethods','update')) {
				$editButton = "
				<a class=\"edit button\"
					href=\"".BASE_URL."/contactMethods/update?contactMethod_id={$method->getId()}\">
					Edit
				</a>
				";
			}
			$name = View::escape($method->getName());
			echo "<li>$editButton $name</li>";
		}
	?>
	</ul>
</div>