<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Ticket $this->ticket
 * @param Issue $this->issue
 * @param Department $this->currentDepartment
 */
$category    = $this->ticket->getCategory();
$category_id = $category ? $category->getId() : '';
$title       = $category ? "New {$category->getName()} Case" : 'New Case';
?>
<div id="addTicketForm">
	<h2><?= self::escape($title); ?></h2>
	<?php $this->_include('departments/chooseDepartmentForm.inc'); ?>
	<form method="post" action="<?= BASE_URL; ?>/tickets/add">
		<fieldset><legend><?= $this->_(array('category', 'categories', 1)); ?></legend>
		<?php
			$this->template->addToAsset('scripts', JQUERY.'/jquery.min.js');
			$this->template->addToAsset('scripts', BASE_URI.'/js/tickets/chooseCategory.js');
			$this->return_url = BASE_URL.'/tickets/add';
			$this->_include('tickets/partials/chooseCategoryFields.inc');
		?>
		</fieldset>

		<fieldset class="ticketFields">
			<legend><?= $this->_('info_location'); ?></legend>
			<table>
				<tr><td><label for="location"><?= $this->_('location'); ?></label></td>
					<td><input name="location" id="location"
							value="<?= self::escape($this->ticket->getLocation()); ?>" />
					</td>
				</tr>
				<tr><td><label for="city"><?= $this->_('city'); ?></label></td>
					<td><input name="city" id="city"
							value="<?= self::escape($this->ticket->getCity()); ?>" />
					</td>
				</tr>
				<tr><td><label for="state"><?= $this->_('state'); ?></label></td>
					<td><input name="state" id="state"
							value="<?= self::escape($this->ticket->getState()); ?>" />
					</td>
				</tr>
				<tr><td><label for="zip"><?= $this->_('zip'); ?></label></td>
					<td><input name="zip" id="zip"
							value="<?= self::escape($this->ticket->getZip()); ?>" />
					</td>
				</tr>
				<tr><td><label for="latitude"><?= $this->_('latitude'); ?></label></td>
					<td><input name="latitude" id="latitude"
							value="<?= $this->ticket->getLatitude(); ?>" />
					</td>
				</tr>
				<tr><td><label for="longitude"><?= $this->_('longitude'); ?></label></td>
					<td><input name="longitude" id="longitude"
							value="<?= $this->ticket->getLongitude(); ?>" />
					</td>
				</tr>
			</table>
		</fieldset>
		<fieldset class="issueFields">
			<legend><?= $this->_('info_issue'); ?></legend>
			<?php
				$this->_include('tickets/issueFields.inc');

				$this->category = $category;
				$this->_include('tickets/issueCustomFields.inc');
			?>
		</fieldset>
		<fieldset class="assignmentFields">
			<legend><?= $this->_('info_assignment'); ?></legend>
			<?php
				$this->_include('tickets/assignTicketFields.inc');
			?>
		</fieldset>


		<fieldset>
			<?php
				$helper = $this->template->getHelper('saveAndCancelButtons');
				echo $helper->saveAndCancelButtons(BASE_URI.'/tickets');
			?>
		</fieldset>
	</form>
</div>
