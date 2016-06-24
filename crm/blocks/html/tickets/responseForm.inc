<?php
/**
 * @copyright 2011-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Issue $this->issue
 */
use Application\Models\ContactMethodTable;
use Blossom\Classes\View;
?>
<div class="responseForm">
	<h2><?= $this->_('add_response'); ?></h2>
	<form method="post" action="<?= $_SERVER['REDIRECT_URL']; ?>">
		<fieldset><legend><?= $this->_('messages.how_did_you_respond'); ?></legend>
			<input name="issue_id" type="hidden" value="<?= $this->issue->getId(); ?>" />
			<table>
				<tr><td><label for="contactMethod_id">
						<?= $this->_(array('contactMethod', 'contactMethods', 1)); ?>
						</label>
					</td>
					<td><select name="contactMethod_id" id="contactMethod_id">
						<?php
							$table = new contactMethodTable();
							$list = $table->find();
							foreach ($list as $method) {
								$name = self::escape($method->getName());
								$selected = $method->getId() == $this->issue->getContactMethod_id()
									? 'selected="selected"'
									: '';
								echo "<option value=\"{$method->getId()}\" $selected>$name</option>";
							}
						?>
						</select>
					</td>
				</tr>
			</table>
			<div><label for="notes"><?= $this->_(array('comment', 'comments', 2)); ?></label></div>
			<div><textarea name="notes" id="notes"></textarea></div>
			<?php
				$helper = $this->template->getHelper('saveAndCancelButtons');
				echo $helper->saveAndCancelButtons($this->issue->getTicket()->getURL());
			?>
		</fieldset>
	</form>
</div>
