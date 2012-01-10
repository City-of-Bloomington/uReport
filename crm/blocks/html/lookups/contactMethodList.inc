<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
?>
<div class="contactMethodList">
	<h1>Contact Methods
		<?php
			if (userIsAllowed('Lookups')) {
				echo $this->template->linkButton(
					'Edit',BASE_URL.'/lookups/updateContactMethods.php','edit'
				);
			}
		?>
	</h1>
	<p>
		These are the available contact methods for the system.
		They are used both for how the citizen contacted the city and for
		how the citizen would like to be contacted.
	</p>
	<ul>
	<?php
		foreach (Lookups::get('contactMethods') as $method) {
			$method = View::escape($method);
			echo "<li>$method</li>";
		}
	?>
	</ul>
</div>