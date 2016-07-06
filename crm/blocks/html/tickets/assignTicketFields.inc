<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Ticket     $this->ticket
 * @param Department $this->currentDepartment
 */
use Application\Models\Department;
use Application\Models\DepartmentTable;

$this->template->addToAsset('scripts', JQUERY.'/jquery.min.js');
$this->template->addToAsset('scripts', BASE_URI.'/js/tickets/chooseDepartment.js');

// There may not be a currentDepartment.
// This can happen when the current user is not in a department
$currentDepartment = $this->currentDepartment ? $this->currentDepartment : new Department();

// If the assigned person is in the currently chosen department, use the assigned person
// Otherwise, use the default person from the currently chosen department
$assignedPerson = $this->ticket->getAssignedPerson();
if (!$assignedPerson) {
	$assignedPerson = $_SESSION['USER'];
}

$selected_id = $assignedPerson->getDepartment_id() == $currentDepartment->getId()
	? $assignedPerson->getId()
	: $currentDepartment->getDefaultPerson_id();


$helper  = $this->template->getHelper('field');

$table   = new DepartmentTable();
$list    = $table->find();
$options = [];
foreach ($list as $d) { $options[] = ['value'=>$d->getId(), 'label'=>self::escape($d->getName())]; }
echo $helper->field([
    'name'    => 'department_id',
    'id'      => 'department_id',
    'label'   => $this->_('department'),
    'type'    => 'select',
    'value'   => $currentDepartment->getId(),
    'options' => $options
]);

$options = [];
foreach ($currentDepartment->getPeople() as $p) { $options[] = ['value'=>$p->getId(), 'label'=>self::escape($p->getFullname())]; }
echo $helper->field([
    'name'    => 'assignedPerson_id',
    'id'      => 'assignedPerson_id',
    'label'   => $this->_('person'),
    'type'    => 'select',
    'value'   => $selected_id,
    'options' => $options
]);

echo $helper->field([
    'name'  => 'notes',
    'id'    => 'notes',
    'label' => $this->_('assignment_notes'),
    'type'  => 'textarea'
]);
