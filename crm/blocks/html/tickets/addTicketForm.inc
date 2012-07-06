<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Ticket $this->ticket
 * @param Issue $this->issue
 * @param Department $this->currentDepartment
 */
$category    = $this->ticket->getCategory();
$category_id = $category ? $category->getId() : '';
$title       = $category ? "New {$category->getName()} Case" : 'New Case';
?>
<div id="addTicketForm">
	<h2><?php echo View::escape($title); ?></h2>
	<?php include APPLICATION_HOME.'/blocks/html/departments/chooseDepartmentForm.inc'; ?>
	<form method="post" action="<?php echo BASE_URL; ?>/tickets/add">
		<fieldset><legend>Category</legend>
		<?php
			$this->template->addToAsset('scripts',YUI.'/yui/yui-min.js');
			$this->template->addToAsset('scripts',BASE_URI.'/js/tickets/chooseCategory.js');
			$this->return_url = BASE_URL.'/tickets/add';
			include APPLICATION_HOME.'/blocks/html/tickets/partials/chooseCategoryFields.inc';
		?>
		</fieldset>

		<fieldset class="ticketFields">
			<legend>Location Info</legend>
			<table>
				<tr><td><label for="location">Location</label></td>
					<td><input name="location" id="location"
							value="<?php echo View::escape($this->ticket->getLocation()); ?>" />
					</td>
				</tr>
				<tr><td><label for="city">City</label></td>
					<td><input name="city" id="city"
							value="<?php echo View::escape($this->ticket->getCity()); ?>" />
					</td>
				</tr>
				<tr><td><label for="state">State</label></td>
					<td><input name="state" id="state"
							value="<?php echo View::escape($this->ticket->getState()); ?>" />
					</td>
				</tr>
				<tr><td><label for="zip">Zip</label></td>
					<td><input name="zip" id="zip"
							value="<?php echo View::escape($this->ticket->getZip()); ?>" />
					</td>
				</tr>
				<tr><td><label for="latitude">Latitude</label></td>
					<td><input name="latitude" id="latitude"
							value="<?php echo $this->ticket->getLatitude(); ?>" />
					</td>
				</tr>
				<tr><td><label for="longitude">Longitude</label></td>
					<td><input name="longitude" id="longitude"
							value="<?php echo $this->ticket->getLongitude(); ?>" />
					</td>
				</tr>
			</table>
		</fieldset>
		<fieldset class="issueFields">
			<legend>Issue Information</legend>
			<?php
				include APPLICATION_HOME.'/blocks/html/tickets/issueFields.inc';

				$this->category = $category;
				include APPLICATION_HOME.'/blocks/html/tickets/issueCustomFields.inc';
			?>
		</fieldset>
		<fieldset class="assignmentFields">
			<legend>Assignment Information</legend>
			<?php
				include APPLICATION_HOME.'/blocks/html/tickets/assignTicketFields.inc';
			?>
		</fieldset>


		<fieldset>
			<button type="submit" class="submit">Submit</button>
			<a class="cancel" href="<?php echo BASE_URL; ?>/tickets">Cancel</a>
		</fieldset>
	</form>
</div>
