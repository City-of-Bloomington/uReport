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
			?>
			<button type="submit" class="submit">Submit</button>
			<button type="button" class="cancel"
				onclick="document.location.href='<?php echo $this->ticket->getURL(); ?>';">
				Cancel
			</button>
		</fieldset>
	</form>
</div>