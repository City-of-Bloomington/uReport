<?php
/**
 * @copyright 2011-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Department this->department
 * @param string $this->action The URL for posting the form
 * @param string $this->return_url
 */
$title = $this->department->getId()
	? $this->translate('labels.edit')
	: $this->translate('labels.add');
$return_url = $this->return_url ? $this->return_url : BASE_URI.'/departments';
?>
<div class="updateDepartmentForm">
<h2><?php echo $title.' '.$this->translate(array('labels.department', 'labels.departments', 1)); ?></h2>
<form method="post" action="<?php echo $this->action; ?>">
	<fieldset>
		<legend>
		<?php
			echo $this->translate(array('labels.department', 'labels.departments', 1)).' '.
				 $this->translate('labels.info')
		?>
		</legend>
		<input name="department_id" type="hidden" value="<?php echo $this->department->getId(); ?>" />
		<table>
			<tr><td><label for="name" class="required">
						<?php echo $this->translate('labels.name'); ?>
						<span class="icon-asterisk"></span>
					</label>
				</td>
				<td><input name="name" id="name"
						value="<?php echo View::escape($this->department->getName()); ?>" />
				</td>
			</tr>

			<tr><td><label for="defaultPerson_id"><?php echo $this->translate('labels.defaultPerson'); ?></label></td>
				<td><select name="defaultPerson_id" id="defaultPerson_id">
						<option value=""></option>
						<?php
							$defaultPerson = $this->department->getDefaultPerson();
							if ($defaultPerson) {
								$name = View::escape($defaultPerson->getFullname());
								echo "<option value=\"{$defaultPerson->getId()}\" selected=\"selected\">$name</option>";
							}
							$people = $this->department->getPeople();
							if ($people) {
								foreach ($people as $person) {
									$name = View::escape($person->getFullname());
									if ($name) {
										echo "<option value=\"{$person->getId()}\">$name</option>";
									}
								}
							}
						?>
					</select>
				</td>
			</tr>
		</table>
	</fieldset>
	<fieldset class="actions">
		<legend><?php echo $this->translate(array('labels.action', 'labels.actions', 2)); ?></legend>
		<ul>
		<?php
			$list = new ActionList();
			$list->find();
			foreach ($list as $action) {
				if ($action->getType()=='department') {
					$id = $action->getId();
					$name = View::escape($action->getName());
					$checked = $this->department->hasAction($action)
						? 'checked="checked"'
						: '';
					echo "
					<li><label>
							<input name=\"actions[$id]\" type=\"checkbox\" $checked />
							$name
						</label>
					</li>
					";
				}
			}
		?>
		</ul>
	</fieldset>
	<fieldset class="categories">
		<legend><?php echo $this->translate(array('labels.category', 'labels.categories', 2)); ?></legend>
		<ul>
		<?php
			$list = new CategoryList();
			$list->find();
			foreach ($list as $category) {
				$id = $category->getId();
				$name = View::escape($category->getName());
				$checked = $this->department->hasCategory($category)
					? 'checked="checked"'
					: '';
				echo "
				<li><label>
						<input name=\"categories[$id]\" type=\"checkbox\" $checked />
						$name
					</label>
				</li>
				";
			}
		?>
		</ul>
	</fieldset>
	<fieldset>
		<?php
			$helper = $this->template->getHelper('saveAndCancelButtons');
			echo $helper->saveAndCancelButtons($return_url);
		?>
	</fieldset>
</form>
</div>
