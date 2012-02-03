<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
?>
<div class="searchForm">
	<?php
		if (userIsAllowed('tickets','add')) {
			$number = isset($_GET['number']) ? (int)$_GET['number'] : '';
			echo "
			<div class=\"quickSearches\">
				<h1>Existing Cases</h1>
				<h2>Quick Search</h2>
				<form method=\"get\" action=\"".BASE_URL."/tickets\">
					<fieldset>
						<label>Case # <input name=\"number\" value=\"$number\"/></label>
						<button type=\"submit\" class=\"search\">Search</button>
					</fieldset>
				</form>
				<ul><li><a href=\"".BASE_URL."/tickets?status=open\">
							All open cases
						</a>
					</li>
					<li><a href=\"".BASE_URL."/tickets?status=open;assignedPerson[]={$_SESSION['USER']->getId()}\">
							All open cases assigned to me
						</a>
					</li>
				";
				if ($_SESSION['USER']->getDepartment()) {
					$dept = $_SESSION['USER']->getDepartment();
					echo "
					<li><a href=\"".BASE_URL."/tickets?status=open;department[]=$dept[_id]\">
							All open cases for my department
						</a>
					</li>
					";
				}
			echo "
				</ul>
			</div>
			";
		}
	?>
	<div class="advancedSearch">
		<h2>Advanced Search</h2>
		<form method="get" action="<?php echo BASE_URL; ?>/tickets">
			<fieldset id="advanced-search">
				<table>
					<tr><td><label for="department">Department</label></td></tr>
					<tr><td><select name="department[]" id="department" multiple="multiple" size="5">
							<?php
								$list = new DepartmentList();
								$list->find();
								foreach ($list as $department) {
									$selected = (isset($_GET['department'])
												&& in_array("{$department->getId()}",$_GET['department']))
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
										echo "<select name=\"{$key}[]\" id=\"$key\" multiple=\"multiple\" size=\"5\">";
										$options = array();
										foreach (Ticket::getDistinct($key) as $value) {
											if (trim($value)) {
												$options[] = $value;
											}
										}
										sort($options);
										foreach ($options as $value) {
											$selected = (isset($_GET[$key]) && in_array($value,$_GET[$key]))
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
					<tr><td><select name="type" id="type">
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
					<?php
						$person = isset($_SESSION['USER']) ? $_SESSION['USER'] : 'anonymous';
						$list = new CategoryList(array('displayableTo'=>$person));
						if (count($list)) {
						echo "
						<tr><td><label for=\"category\">Category</label></td></tr>
						<tr><td><select name=\"category[]\" id=\"category\" multiple=\"multiple\" size=\"5\">
						";
							foreach ($list as $category) {
								$selected = (isset($_GET['category'])
											&& in_array("{$category->getId()}",$_GET['category']))
									? 'selected="selected"'
									: '';
								$name = View::escape($category);
								echo "<option value=\"{$category->getId()}\" $selected>$name</option>";
							}
						echo "
								</select>
							</td>
						</tr>
						";
						}
					?>
					<tr><td><label for="status">Status</label></td></tr>
					<tr><td><select name="status" id="status">
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
					<tr><td><label for="start_date">Case Created Date </label> (e.g. 1/1/2011)</td></tr>
					<tr><td><table>
								<tr><td><label for="start_date">Start: </label></td>
									<td><input name="start_date" id="start_date" size="10" maxlength="10"
											value="<?php echo !empty($_GET['start_date']) ? View::escape($_GET['start_date']) : ''; ?>" />
									</td>
								</tr>
								<tr><td><label for="end_date">End:</label></td>
									<td><input name="end_date" id="end_date" size="10" maxlength="10"
											value="<?php echo !empty($_GET['end_date']) ? View::escape($_GET['end_date']) : ''; ?>" />
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr><td><label for="client_id">Client</label></td></tr>
					<tr><td><select name="client_id" id="client_id">
								<option></option>
								<?php
									$clients = new ClientList();
									$clients->find();
									foreach ($clients as $client) {
										$selected = (isset($_GET['client_id']) && $_GET['client_id']=="{$client->getId()}")
											? 'selected="selected"'
											: '';
										$name = View::escape($client->getName());
										echo "<option value=\"{$client->getId()}\">$name</option>";
									}
								?>
							</select>
						</td>
					</tr>
				</table>
			</fieldset>

			<fieldset><legend>Labels</legend>
				<ul>
				<?php
					foreach (Lookups::get('labels') as $label) {
						$checked = (isset($_GET['labels']) && in_array($label,$_GET['labels']))
							? 'checked="checked"'
							: '';
						$label = View::escape($label);
						echo "
						<li><label>
								<input name=\"labels[]\" type=\"checkbox\" value=\"$label\" $checked />
								$label
							</label>
						</li>
						";
					}
				?>
				</ul>
			</fieldset>

			<fieldset><legend>Fields to display</legend>
				<ul class="fields">
				<?php
					// Default columns to display
					if (!isset($_GET['fields'])) {
						$_GET['fields'] = TicketList::$defaultFieldsToDisplay;
					}

					foreach (TicketList::getDisplayableFields() as $name=>$definition) {
						if ((false === strpos($name,'Person') || userIsAllowed('people','view'))
							&& $name!='id') {
							$checked = isset($_GET['fields'][$name]) ? 'checked="checked"' : '';
							$value = View::escape($definition['displayName']);
							echo "
							<li><label>
									<input name=\"fields[$name]\" id=\"fields-$name\" type=\"checkbox\" $checked />
									$value
								</label>
							</li>
							";
						}
					}
				?>
				</ul>
			</fieldset>


			<fieldset><legend>Submit</legend>
				<?php
					if (isset($_GET['report'])) {
						$report = View::escape($_GET['report']);
						echo "<input name=\"report\" type=\"hidden\" value=\"$report\" />";
					}
				?>
				<button type="submit" class="search">Search</button>
			</fieldset>
		</form>
	</div>
</div>
