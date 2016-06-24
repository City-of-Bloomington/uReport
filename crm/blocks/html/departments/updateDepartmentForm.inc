<?php
/**
 * @copyright 2011-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Department this->department
 * @param string $this->action The URL for posting the form
 * @param string $this->return_url
 */
use Application\Models\ActionTable;
use Application\Models\CategoryTable;
use Blossom\Classes\View;

$title = $this->department->getId()
	? $this->_('labels.edit')
	: $this->_('labels.add');
$return_url = $this->return_url ? $this->return_url : BASE_URI.'/departments';
?>
<div class="updateDepartmentForm">
<h2><?= $title.' '.$this->_(array('labels.department', 'labels.departments', 1)); ?></h2>
<form method="post" action="<?= $this->action; ?>">
	<fieldset>
		<legend>
		<?php
			echo $this->_(array('labels.department', 'labels.departments', 1)).' '.
				 $this->_('labels.info')
		?>
		</legend>
		<input name="department_id" type="hidden" value="<?= $this->department->getId(); ?>" />
		<table>
			<tr><td><label for="name" class="required">
						<?= $this->_('labels.name'); ?>
						<span class="fa fa-asterisk"></span>
					</label>
				</td>
				<td><input name="name" id="name"
						value="<?= self::escape($this->department->getName()); ?>" />
				</td>
			</tr>

			<tr><td><label for="defaultPerson_id"><?= $this->_('labels.defaultPerson'); ?></label></td>
				<td><select name="defaultPerson_id" id="defaultPerson_id">
						<option value=""></option>
						<?php
							$defaultPerson = $this->department->getDefaultPerson();
							if ($defaultPerson) {
								$name = self::escape($defaultPerson->getFullname());
								echo "<option value=\"{$defaultPerson->getId()}\" selected=\"selected\">$name</option>";
							}
							$people = $this->department->getPeople();
							if ($people) {
								foreach ($people as $person) {
									$name = self::escape($person->getFullname());
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
		<legend><?= $this->_(array('labels.action', 'labels.actions', 2)); ?></legend>
		<ul>
		<?php
			$t = new ActionTable();
			$list = $t->find();
			foreach ($list as $action) {
				if ($action->getType()=='department') {
					$id = $action->getId();
					$name = self::escape($action->getName());
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
		<legend><?= $this->_(array('labels.category', 'labels.categories', 2)); ?></legend>
		<ul>
		<?php
			$t = new CategoryTable();
			$list = $t->find();
			foreach ($list as $category) {
				$id = $category->getId();
				$name = self::escape($category->getName());
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
