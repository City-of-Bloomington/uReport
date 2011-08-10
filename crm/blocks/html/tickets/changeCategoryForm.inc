<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Ticket $this->ticket
 */
?>
<div class="changeCategoryForm">
	<form method="post" action="<?php echo BASE_URL; ?>/tickets/changeCategory.php">
		<fieldset><legend>Change Category</legend>
			<input name="ticket_id" type="hidden" value="<?php echo $this->ticket->getId(); ?>" />
			<?php
				$this->return_url = BASE_URL.'/tickets/changeCategory.php?ticket_id='.$this->ticket->getId();
				include APPLICATION_HOME.'/blocks/html/tickets/partials/chooseCategoryFields.inc';

				echo $this->template->formButton('Submit','submit','submit');
				echo $this->template->formButton(
					'Cancel','button','cancel',null,"document.location.href='{$this->ticket->getURL()}';"
				);
			?>
		</fieldset>
	</form>
</div>