<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Issue $this->issue
 */
use Application\Models\ContactMethodTable;
use Application\Models\IssueTypeTable;
use Application\Models\LabelTable;

$helper = $this->template->getHelper('field');
$table   = new IssueTypeTable();
$list    = $table->find();
$options = [];
foreach ($list as $type) {
    $options[] = ['value'=>$type->getId(), 'label'=>self::escape($type->getName())];
}
echo $helper->field([
    'name'     => 'issueType_id',
    'id'       => 'issueType_id',
    'label'    => $this->_('type'),
    'value'    => $this->issue->getIssueType_id(),
    'required' => true,
    'type'     => 'select',
    'options'  => $options
]);

echo $helper->field([
    'name'  => 'reportedByPerson',
    'id'    => 'reportedByPerson_id',
    'label' => $this->_('reportedByPerson'),
    'type'  => 'person',
    'value' => $this->issue->getReportedByPerson()
]);

$options = [];
$table = new ContactMethodTable();
$contactMethods = $table->find();
$contactMethods->buffer();
foreach ($contactMethods as $method) {
    $options[] = ['value'=>$method->getId(), 'label'=>self::escape($method->getName())];
}
echo $helper->field([
    'name'    => 'contactMethod_id',
    'id'      => 'contactMethod_id',
    'label'   => $this->_('contactMethod'),
    'value'   => $this->issue->getContactMethod_id(),
    'type'    => 'select',
    'options' => $options
]);

echo $helper->field([
    'name'    => 'responseMethod_id',
    'id'      => 'responseMethod_id',
    'label'   => $this->_('responseMethod'),
    'value'   => $this->issue->getResponseMethod_id(),
    'type'    => 'select',
    'options' => $options

]);

echo $helper->field([
    'name'  => 'description',
    'id'    => 'description',
    'label' => $this->_('description'),
    'value' => self::escape($this->issue->getDescription())
]);
