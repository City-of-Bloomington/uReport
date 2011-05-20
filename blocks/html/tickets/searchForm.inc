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
						All open cases
					</a>
				</li>
				<li><a href=\"".BASE_URL."/tickets/home.php?assignedPerson={$_SESSION['USER']->getId()}\">
						All cases assigned to me
					</a>
				</li>
			";
			if ($_SESSION['USER']->getDepartment()) {
				$dept = $_SESSION['USER']->getDepartment();
				echo "
				<li><a href=\"".BASE_URL."/tickets/home.php?department=$dept[_id]\">
						All cases for my department
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
		<fieldset id="advanced-search">
			<table>
				<tr><td><label for="enteredByPerson">Entered By</label></td></tr>
				<tr><td><select name="enteredByPerson" id="enteredByPerson">
							<option></option>
							<?php
								foreach (Ticket::getDistinct('enteredByPerson._id') as $id) {
									$person = new Person($id);
									$selected = (isset($_GET['enteredByPerson']) && $_GET['enteredByPerson']==$person->getId())
										? 'selected="selected"'
										: '';
									$name = View::escape($person->getFullname());
									echo "<option value=\"{$person->getId()}\" $selected>$name</option>";
								}
							?>
						</select>
					</td>
				</tr>
				<tr><td><label for="assignedPerson">Assigned To</label></td></tr>
				<tr><td><select name="assignedPerson" id="assignedPerson">
							<option></option>
							<?php
								foreach (Ticket::getDistinct('assignedPerson._id') as $id) {
									$person = new Person($id);
									$selected = (isset($_GET['assignedPerson']) && $_GET['assignedPerson']==$person->getId())
										? 'selected="selected"'
										: '';
									$name = View::escape($person->getFullname());
									echo "<option value=\"{$person->getId()}\" $selected>$name</option>";
								}
							?>
						</select>
					</td>
				</tr>
				<tr><td><label for="department">Department</label></td></tr>
				<tr><td><select name="department" id="department">
							<option></option>
							<?php
								$list = new DepartmentList();
								$list->find();
								foreach ($list as $department) {
									$selected = (isset($_GET['department']) && $_GET['department']=="{$department->getId()}")
										? 'selected="selected"'
										: '';
									$name = View::escape($department->getName());
									echo "<option value=\"{$department->getId()}\" $selected>$name</option>";
								}
							?>
						</select>
					</td>
				</tr>
		
				<?php
					foreach (AddressService::$customFieldDescriptions as $key=>$config) {
						echo "
						<tr><td><label for=\"$key\">$config[description]</label></td></tr>
						<tr><td>
						";
							switch ($config['formElement']) {
								case 'select':
									echo "<select multiple=\"$key\" id=\"$key\"><option></option>";
									foreach (Ticket::getDistinct($key) as $value) {
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
				<tr><td><label for="type">Type</label></td></tr>
				<tr><td><select multiple="type" id="type">
							<option></option>
							<?php
								foreach (Ticket::getDistinct('issues.type') as $type) {
									$selected = (isset($_GET['type']) && $_GET['type']==$type)
										? 'selected="selected"'
										: '';
									$type = View::escape($type);
									echo "<option value=\"$type\" $selected>$type</option>";
								}
							?>
						</select>
					</td>
				</tr>
				<tr><td><label for="category">Category</label></td></tr>
				<tr><td><select multiple="category" id="category">
							<option></option>
							<?php
								$list = new CategoryList();
								$list->find();
								foreach ($list as $category) {
									$selected = (isset($_GET['category']) && $_GET['category']=="{$category->getId()}")
										? 'selected="selected"'
										: '';
									$name = View::escape($category);
									echo "<option value=\"{$category->getId()}\" $selected>$name</option>";
								}
							?>
						</select>
					</td>
				</tr>
				<tr><td><label for="status">Status</label></td></tr>
				<tr><td><select multiple="status" id="status">
							<option></option>
							<?php
								foreach (Ticket::getDistinct('status') as $status) {
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
			</table>
		</fieldset>

		<fieldset><legend>Fields to display</legend>
			<ul class="fields">
			<?php
				// Default columns to display
				if (!isset($_GET['fields'])) {
					$_GET['fields'] = array(
						'id'=>'on',
						'status'=>'on',
						'location'=>'on',
						'enteredDate'=>'on',
						'enteredByPerson'=>'on'
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


		<fieldset><legend>Submit</legend>
			<?php
				echo $this->template->formButton(
					'Search','submit','search'
				);
			?>
		</fieldset>
	</form>
</div>
<?php
	#$this->template->addToAsset('scripts',YUI.'/yui/yui-min.js');
	#$this->template->addToAsset('scripts',BASE_URL.'/js/tickets/search.js');
?>
