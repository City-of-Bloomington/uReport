<?php
/**
 * Displays the fields the user searched on
 *
 * @copyright 2012-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$currentURL = new URL($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
?>
<div class="searchParameters">
	<?php
		echo '<div>';
		if (   !empty($_GET['enteredDate'])
			|| !empty($_GET['departments'])
			|| !empty($_GET['categories'])) {
			if (userIsAllowed('bookmarks', 'update')) {
				$bookmarkForm = new Block('bookmarks/updateForm.inc',array('type'=>'report'));
				echo $bookmarkForm->render('html', $this->template);
			}
		}
		echo '</div>';
	?>
	<span>
	<?php
		if (!empty($_GET['enteredDate']['start']) || !empty($_GET['enteredDate']['end'])) {
			$deleteURL = clone $currentURL;
			unset($deleteURL->enteredDate);

			$s = !empty($_GET['enteredDate']['start']) ? $_GET['enteredDate']['start'] : '*';
			$e = !empty($_GET['enteredDate']['end'])   ? $_GET['enteredDate']['end']   : '*';
			echo "
			<a class=\"btn\" href=\"$deleteURL\">
				<span class=\"icon-remove\"></span>
				$s to $e
			</a>
			";
		}
		if (!empty($_GET['departments'])) {
			foreach (array_keys($_GET['departments']) as $id) {
				try {
					$d = new Department($id);
					$deleteURL = clone $currentURL;
					unset($deleteURL->parameters['departments'][$id]);
					$name = View::escape($d->getName());
					echo "
					<a class=\"btn\" href=\"$deleteURL\">
						<span class=\"icon-remove\"></span>
						$name
					</a>
					";
				}
				catch (Exception $e) {
				}
			}
		}
		if (!empty($_GET['categories'])) {
			foreach (array_keys($_GET['categories']) as $id) {
				try {
					$c = new Category($id);
					$deleteURL = clone $currentURL;
					unset($deleteURL->parameters['categories'][$id]);
					$name = View::escape($c->getName());
					echo "
					<a class=\"btn\" href=\"$deleteURL\">
						<span class=\"icon-remove\"></span>
						$name
					</a>
					";
				}
				catch (Exception $e) {
				}
			}
		}
	?>
	</span>
</div>
