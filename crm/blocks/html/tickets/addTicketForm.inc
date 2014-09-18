<?php
/**
 * @copyright 2011-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Ticket $this->ticket
 * @param Issue $this->issue
 * @param Department $this->currentDepartment
 */
use Blossom\Classes\View;

$category    = $this->ticket->getCategory();
$category_id = $category ? $category->getId() : '';
$title       = $category ? "New {$category->getName()} Case" : 'New Case';
?>
<div id="addTicketForm">
	<h2><?php echo View::escape($title); ?></h2>
	<?php include APPLICATION_HOME.'/blocks/html/departments/chooseDepartmentForm.inc'; ?>
	<form method="post" action="<?php echo BASE_URL; ?>/tickets/add">
		<fieldset><legend><?php echo $this->translate(array('labels.category', 'labels.categories', 1)); ?></legend>
		<?php
			$this->template->addToAsset('scripts', JQUERY.'/jquery.min.js');
			$this->template->addToAsset('scripts', BASE_URI.'/js/tickets/chooseCategory.js');
			$this->return_url = BASE_URL.'/tickets/add';
			include APPLICATION_HOME.'/blocks/html/tickets/partials/chooseCategoryFields.inc';
		?>
		</fieldset>

		<fieldset class="ticketFields">
			<legend><?php echo $this->translate('labels.info_location'); ?></legend>
			<table>
				<tr><td><label for="location"><?php echo $this->translate(array('labels.location', 'labels.locations', 1)); ?></label></td>
					<td><input name="location" id="location"
							value="<?php echo View::escape($this->ticket->getLocation()); ?>" />
					</td>
				</tr>
				<tr><td><label for="city"><?php echo $this->translate('labels.city'); ?></label></td>
					<td><input name="city" id="city"
							value="<?php echo View::escape($this->ticket->getCity()); ?>" />
					</td>
				</tr>
				<tr><td><label for="state"><?php echo $this->translate('labels.state'); ?></label></td>
					<td><input name="state" id="state"
							value="<?php echo View::escape($this->ticket->getState()); ?>" />
					</td>
				</tr>
				<tr><td><label for="zip"><?php echo $this->translate('labels.zip'); ?></label></td>
					<td><input name="zip" id="zip"
							value="<?php echo View::escape($this->ticket->getZip()); ?>" />
					</td>
				</tr>
				<tr><td><label for="latitude"><?php echo $this->translate('labels.latitude'); ?></label></td>
					<td><input name="latitude" id="latitude"
							value="<?php echo $this->ticket->getLatitude(); ?>" />
					</td>
				</tr>
				<tr><td><label for="longitude"><?php echo $this->translate('labels.longitude'); ?></label></td>
					<td><input name="longitude" id="longitude"
							value="<?php echo $this->ticket->getLongitude(); ?>" />
					</td>
				</tr>
			</table>
		</fieldset>
		<fieldset class="issueFields">
			<legend><?php echo $this->translate('labels.info_issue'); ?></legend>
			<?php
				include APPLICATION_HOME.'/blocks/html/tickets/issueFields.inc';

				$this->category = $category;
				include APPLICATION_HOME.'/blocks/html/tickets/issueCustomFields.inc';
			?>
		</fieldset>
		<fieldset class="assignmentFields">
			<legend><?php echo $this->translate('labels.info_assignment'); ?></legend>
			<?php
				include APPLICATION_HOME.'/blocks/html/tickets/assignTicketFields.inc';
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
