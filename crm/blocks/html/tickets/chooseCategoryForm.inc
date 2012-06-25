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
 */
$this->template->addToAsset('scripts',YUI.'/yui/yui-min.js');
$this->template->addToAsset('scripts',BASE_URI.'/js/tickets/chooseCategory.js');
?>
<div id="chooseCategoryForm">
	<form method="get" action="<?php echo BASE_URI; ?>/tickets/add">
		<fieldset><legend>Category</legend>
			<?php
				$block = new Block(
					'tickets/partials/chooseCategoryFields.inc',
					array('return_url'=>BASE_URL.'/tickets/add')
				);
				echo $block->render('html',$this->template);

				$h = $this->template->getHelper('renderInputs');
				echo $h->renderInputs($_REQUEST, null, array('category_id'));
			?>
			<button type="submit" class="edit">Change Category</button>
		</fieldset>
	</form>
</div>
