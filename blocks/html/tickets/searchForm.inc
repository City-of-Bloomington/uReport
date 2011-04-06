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
				'Add New Ticket',BASE_URL.'/tickets/addTicket.php','add',null,null,'button'
			);
			echo "
			<h1>Quick Search</h1>
			<ul><li><a href=\"".BASE_URL."/tickets/home.php?status=open\">
						All open tickets
					</a>
				</li>
				<li><a href=\"".BASE_URL."/tickets/home.php?assignedPerson_id={$_SESSION['USER']->getPerson_id()}\">
						All tickets assigned to me
					</a>
				</li>
			";
			if ($_SESSION['USER']->getDepartment_id()) {
				echo "
				<li><a href=\"".BASE_URL."/tickets/home.php?department_id={$_SESSION['USER']->getDepartment_id()}\">
						All tickets for my department
					</a>
				</li>
				";
			}
			echo "
			</ul>
			";
		}
	?>
	</div>

	<h1>Advanced Search</h1>
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
								$ids = Ticket::getDistinct('enteredByPerson_id');
								foreach ($ids as $id) {
									$person = new Person($id);
									$name = View::escape($person->getFullname());
									$selected = (isset($_GET['enteredByPerson_id']) && $_GET['enteredByPerson_id']==$person->getId())
										? 'selected="selected"'
										: '';
									echo "<option value=\"{$person->getId()}\" $selected>$name</option>";
								}
							?>
						</select>
					</td>
				</tr>
				<tr><td><label for="assignedPerson_id">Assigned To</label></td>
					<td><select name="assignedPerson_id" id="assignedPerson_id">
							<option></option>
							<?php
								$ids = Ticket::getDistinct('assignedPerson_id');
								foreach ($ids as $id) {
									$person = new Person($id);
									$name = View::escape($person->getFullname());
									$selected = (isset($_GET['assignedPerson_id']) && $_GET['assignedPerson_id']==$person->getId())
										? 'selected="selected"'
										: '';
									echo "<option value=\"{$person->getId()}\" $selected>$name</option>";
								}
							?>
						</select>
					</td>
				</tr>
				<tr><td><label for="department_id">Department</label></td>
					<td><select name="department_id" id="department_id">
							<option></option>
							<?php
								$list = new DepartmentList();
								$list->find();
								foreach ($list as $department) {
									$name = View::escape($department->getName());
									$selected = (isset($_GET['department_id']) && $_GET['department_id']==$department->getId())
										? 'selected="selected"'
										: '';
									echo "<option value=\"{$department->getId()}\" $selected>$name</option>";
								}
							?>
						</select>
					</td>
				</tr>
				<tr><td><label for="city">City</label></td>
					<td><select name="city" id="city">
							<option></option>
							<?php
								foreach (Ticket::getDistinct('city') as $city) {
									$selected = (isset($_GET['city']) && $_GET['city']==$city)
										? 'selected="selected"'
										: '';
									$city = View::escape($city);
									echo "<option $selected>$city</option>";
								}

							?>
						</select>
					</td>
				</tr>
				<tr><td><label for="state">State</label></td>
					<td><select name="state" id="state">
							<option></option>
							<?php
								foreach (Ticket::getDistinct('state') as $state) {
									$selected = (isset($_GET['state']) && $_GET['state']==$state)
										? 'selected="selected"'
										: '';
									$state = View::escape($state);
									echo "<option $selected>$state</option>";
								}
							?>
						</select>
					</td>
				</tr>
				<tr><td><label for="zip">Zip</label></td>
					<td><select name="zip" id="zip">
							<option></option>
							<?php
								foreach (Ticket::getDistinct('zip') as $zip) {
									$selected = (isset($_GET['zip']) && $_GET['state']==$zip)
										? 'selected="selected"'
										: '';
									$zip = View::escape($zip);
									echo "<option $selected>$zip</option>";
								}
							?>
						</select>
					</td>
				</tr>
				<?php
					foreach (AddressService::$customFieldDescriptions as $key=>$config) {
						echo "
						<tr><td><label for=\"$key\">$config[description]</label></td>
							<td>
						";
							switch ($config['formElement']) {
								case 'select':
									echo "<select name=\"$key\" id=\"$key\"><option></option>";
									foreach (AddressService::getDistinct($key) as $value) {
										$selected = (isset($_GET[$key]) && $_GET[$key]==$value)
											? 'selected="selected"'
											: '';
										echo "<option $selected>$value</option>";
									}
									echo "</select>";
									break;

								default:
									$value = isset($_GET[$key]) ? View::escape($_GET[$key]) : '';
									echo "<input name=\"$key\" id=\"$key\" value=\"$value\" />";
							}
						echo "
							</td>
						</tr>
						";
					}
				?>
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
				<tr><td><label for="action_id">Action</label></td>
					<td><select name="action_id" id="action_id">
							<option></option>
							<?php
								$list = new ActionList();
								$list->find();
								foreach ($list as $action) {
									$name = View::escape($action->getName());
									$selected = (isset($_GET['action_id']) && $_GET['action_id']==$action->getId())
										? 'selected="selected"'
										: '';
									echo "<option value=\"{$action->getId()}\" $selected>$name</option>";
								}
							?>
						</select>
					</td>
				</tr>
				<tr><td><label for="actionPerson_id">Person ID</label></td>
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