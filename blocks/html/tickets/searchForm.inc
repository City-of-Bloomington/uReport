<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
?>
<div class="searchForm">
	<div>
	<?php
		if (userIsAllowed('Tickets')) {
			echo $this->linkButton(
				'Add New Request',BASE_URL.'/tickets/updateTicket.php','add',null,null,'largeButton'
			);
		}
	?>
	</div>

	<h1>Find Requests</h1>
	<form method="get" action="<?php echo BASE_URL; ?>/tickets/home.php">
		<fieldset class="fields"><legend>Fields to display</legend>
			<ul>
			<?php
				// Default columns to display
				if (!isset($_GET['fields'])) {
					$_GET['fields'] = array(
						'ticket-id'=>'on',
						'ticket-status'=>'on',
						'ticket-location'=>'on',
						'ticket-enteredDate'=>'on',
						'ticket-enteredByPerson'=>'on'
					);
				}

				foreach (TicketList::getDisplayableFields() as $name=>$label) {
					$checked = isset($_GET['fields'][$name]) ? 'checked="checked"' : '';
					echo "
					<li><label>
							<input name=\"fields[$name]\" id=\"fields-$name\" type=\"checkbox\" $checked />
							$label
						</label>
					</li>
					";
				}
			?>
			</ul>
		</fieldset>

		<fieldset><legend>Ticket Information</legend>
			<table>
				<tr><td><label for="enteredByPerson_id">Entered By</label></td>
					<td><select name="enteredByPerson_id" id="enteredByPerson_id">
							<option></option>
							<?php
								$people = new PersonList(array('enteredTicket'=>true));
								foreach ($people as $person) {
									$name = View::escape($person->getFullname());
									$selected = (isset($_GET['person_id']) && $_GET['person_id']==$person->getId())
										? 'selected="selected"'
										: '';
									echo "<option value=\"{$person->getId()}\" $selected>$name</option>";
								}
							?>
						</select>
					</td>
				</tr>
				<tr><td><label for="location">Location</label></td>
					<td><input name="location" id="location"
							value="<?php echo isset($_GET['location']) ? View::escape($_GET['location']) : ''; ?>" />
					</td>
				</tr>
				<tr><td><label for="neighborhoodAssociation">Neighborhood Assocation</label></td>
					<td><select name="neighborhoodAssociation" id="neighborhoodAssociation">
							<option></option>
							<?php
								foreach (Location::getNeighborhoodAssociations() as $neighborhoodAssociation) {
									$selected = (isset($_GET['neighborhoodAssociation']) && $_GET['neighborhoodAssociation']==$neighborhoodAssociation)
										? 'selected="selected"'
										: '';
									$neighborhoodAssociation = View::escape($neighborhoodAssociation);
									echo "<option $selected>$neighborhoodAssociation</option>";
								}
							?>
						</select>
					</td>
				</tr>
				<tr><td><label for="township">Township</label></td>
					<td><select name="township" id="township">
							<option></option>
							<?php
								foreach (Location::getTownships() as $township) {
									$selected = (isset($_GET['township']) && $_GET['township']==$township)
										? 'selected="selected"'
										: '';
									$township = View::escape($township);
									echo "<option $selected>$township</option>";
								}
							?>
						</select>
					</td>
				</tr>
			</table>
		</fieldset>

		<fieldset><legend>Issue Information</legend>
			<table>
				<tr><td><label for="issueType_id">Type</label></td>
					<td><select name="issueType_id" id="issueType_id">
							<option></option>
							<?php
								$list = new IssueTypeList();
								$list->find();
								foreach ($list as $type) {
									$selected = (isset($_GET['issueType_id']) && $_GET['issueType_id']==$type->getId())
										? 'selected="selected"'
										: '';
									$name = View::escape($type);
									echo "<option value=\"{$type->getId()}\" $selected>$name</option>";
								}
							?>
						</select>
					</td>
				</tr>
				<tr><td><label for="category_id">Category</label></td>
					<td><select name="category_id" id="category_id">
							<option></option>
							<?php
								$list = new CategoryList();
								$list->find();
								foreach ($list as $category) {
									$selected = (isset($_GET['category_id']) && $_GET['category_id']==$category->getId())
										? 'selected="selected"'
										: '';
									$name = View::escape($category);
									echo "<option value=\"{$category->getId()}\" $selected>$name</option>";
								}
							?>
						</select>
					</td>
				</tr>
				<tr><td><label for="contactMethod_id">Contact Method</label></td>
					<td><select name="contactMethod_id" id="contactMethod_id">
							<option></option>
							<?php
								$list = new ContactMethodList();
								$list->find();
								foreach ($list as $contactMethod) {
									$selected = (isset($_GET['contactMethod_id']) && $_GET['contactMethod_id']==$contactMethod->getId())
										? 'selected="selected"'
										: '';
									$name = View::escape($contactMethod);
									echo "<option value=\"{$contactMethod->getId()}\" $selected>$name</option>";
								}
							?>
						</select>
					</td>
				</tr>
			</table>
		</fieldset>

		<fieldset><legend>Actions</legend>
			<table>
				<tr><td><label for="status">Status</label></td>
					<td><select name="status" id="status">
							<option></option>
							<?php
								foreach (TicketHistory::getStatuses() as $status) {
									$selected = (isset($_GET['status']) && $_GET['status']==$status)
										? 'selected="selected"'
										: '';
									$status = View::escape($status);
									echo "<option $selected>$status</option>";
								}
							?>
						</select>
					</td>
				</tr>
				<tr><td><label for="eventLabel">Action</label></td>
					<td><select name="eventLabel" id="eventLabel">
							<option></option>
							<?php
								foreach (TicketHistory::getEventLabels() as $eventLabel) {
									$selected = (isset($_GET['eventLabel']) && $_GET['eventLabel']==$eventLabel)
										? 'selected="selected"'
										: '';
									$eventLabel = View::escape($eventLabel);
									echo "<option $selected>$eventLabel</option>";
								}
							?>
						</select>
					</td>
				</tr>
				<tr><td><label for="person_id">Person ID</label></td>
					<td><input name="person_id" id="person_id"
							value="<?php echo isset($_GET['person_id']) ? View::escape($_GET['person_id']) : ''; ?>" />
					</td>
				</tr>
			</table>
		</fieldset>

		<fieldset><legend>Submit</legend>
			<?php
				echo $this->template->formButton(
					'Search','submit','search'
				);
			?>
		</fieldset>
	</form>
</div>