<?php
/**
 * This form is the starting point for adding a new ticket
 *
 * We need to ask the user for the Category before they start the ticket
 * adding process.  However, to save the user's time, we may be using this
 * in a place where some other piece of ticket information is already defined.
 * Places like viewLocation and viewPerson already have location and person_id
 * defined.  We need to preserve any predefined fields that we can pre-select
 * on the add ticket form.
 *
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param URL $this->return_url
 * @param string $this->title
 */
$title = $this->title ? View::escape($this->title) : 'New Cases';
?>
<div class="addNewForm">
	<h1><?php echo $title; ?></h1>
	<form method="get" action="<?php echo $this->return_url; ?>">
		<fieldset><legend>Choose a category</legend>
			<?php
				include APPLICATION_HOME.'/blocks/html/tickets/partials/chooseCategoryFields.inc';
				// Check for and preserve any fields already in the request
				foreach ($_REQUEST as $key=>$value) {
					if ($key != 'category') {
						$this->return_url->$key = $value;
						$key = View::escape($key);
						$value = View::escape($value);
						echo "<input type=\"hidden\" name=\"$key\" value=\"$value\" />";
					}
				}
				echo $this->template->formButton(
					'Add New Case','submit','add'
				);
			?>
		</fieldset>
	</form>
</div>
