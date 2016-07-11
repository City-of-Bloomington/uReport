<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Application\Models\Person;

$links  = [];
$routes = [
    'people'         => 'person',
    'users'          => 'user',
    'departments'    => 'department',
    'categories'     => 'category',
    'categoryGroups' => 'categoryGroup',
    'substatus'      => 'substatus',
    'actions'        => 'action',
    'clients'        => 'client',
    'contactMethods' => 'contactMethod',
    'issueTypes'     => 'issueType'
];
foreach ($routes as $plural=>$singular) {
    $requiredAction = 'index';
    if (Person::isAllowed($plural, $requiredAction)) {
        $url = BASE_URI."/$plural";
        $links[] = ['url'=>$url, 'label'=>$this->_([$singular, $plural, 2])];
    }
}
if (Person::isAllowed('categories', 'sla')) {
    $url = BASE_URI.'/categories/sla';
    $links[] = ['url'=>$url, 'label'=>$this->_('sla')];
}

if ($links) {
    $helper = $this->getHelper('dropdown');
    echo $helper->dropdown($links, $this->_('admin'), 'Administrator_menu');
}
