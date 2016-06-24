<?php
/**
 * @copyright 2011-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Ticket $this->ticket
 */
use Blossom\Classes\View;
?>
<div class="changeCategoryForm">
	<form method="post" action="<?= BASE_URL; ?>/tickets/changeCategory">
		<fieldset><legend><?= $this->_('change_category'); ?></legend>
			<input name="ticket_id" type="hidden" value="<?= $this->ticket->getId(); ?>" />
			<?php
				// Preserve any extra parameters passed in
				foreach ($_REQUEST as $key=>$value) {
					if ($key!='ticket_id' && $key!='return_url') {
						$key   = self::escape($key);
						$value = self::escape($value);
						echo "<input type=\"hidden\" name=\"$key\" value=\"$value\" />\n";
					}
				}
				$this->return_url = BASE_URL."/tickets/changeCategory.php?ticket_id={$this->ticket->getId()}";
				include APPLICATION_HOME.'/blocks/html/tickets/partials/chooseCategoryFields.inc';
				include APPLICATION_HOME.'/blocks/html/tickets/partials/submitAndCancelButtons.inc';
			?>
		</fieldset>
	</form>
</div>
