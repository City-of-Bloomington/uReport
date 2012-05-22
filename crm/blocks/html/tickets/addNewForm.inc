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
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param string $this->title
 */
$title = $this->title ? View::escape($this->title) : 'New Case';
?>
<div class="addNewForm">
	<h2><?php echo $title; ?></h2>
	<form method="get" action="<?php echo BASE_URI; ?>/tickets/add">
		<fieldset>
			<?php
				$block = new Block(
					'tickets/partials/chooseCategoryFields.inc',
					array('return_url'=>BASE_URL.'/tickets/add')
				);
				echo $block->render('html',$this->template);

				// Check for and preserve any fields already in the request
				function renderInputs($array, $base=null) {
					foreach ($array as $k=>$v) {
						if ($k != 'category') {
							$k = View::escape($k);
							$name = $base ? "{$base}[{$k}]" : $k;
							if (!is_array($v)) {
								$v = View::escape($v);
								echo "<input name=\"$name\" value=\"$v\" type=\"hidden\" />";
							}
							else {
								renderInputs($v, $k);
							}
						}
					}
				}
				renderInputs($_REQUEST);
			?>
			<button type="submit" class="add">Add New Case</button>
		</fieldset>
	</form>
</div>
