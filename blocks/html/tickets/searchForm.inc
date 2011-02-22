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
		<fieldset><legend>Ticket Information</legend>
			<table>
				<tr><td><label for="person_id">Entered By</label></td>
					<td><select name="person_id" id="person_id">
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
								$list = ActionType::getStatuses();
								foreach ($list as $status) {
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
				<tr><td><label for="actionType_id">Action</label></td>
					<td><select name="actionType_id" id="actionType_id">
							<option></option>
							<?php
								$list = new ActionTypeList();
								$list->find();
								foreach ($list as $action) {
									$name = View::escape($action->getFormLabel());
									$selected = (isset($_GET['actionType_id']) && $_GET['actionType_id']==$action->getId())
										? 'selected="selected"'
										: '';
									echo "<option value=\"{$action->getId()}\" $selected>$name</option>";
								}
							?>
						</select>
					</td>
				</tr>
				<tr><td><label for="actionPerson_id">Person</label></td>
					<td><input name="actionPerson_id" id="actionPerson_id"
							value="<?php echo isset($_GET['actionPerson_id']) ? View::escape($_GET['actionPerson_id']) : ''; ?>" />
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