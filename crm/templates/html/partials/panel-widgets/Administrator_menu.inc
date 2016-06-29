<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Application\Models\Person;

$links = '';
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
        $links.= "<a href=\"$url\">{$this->_([$singular, $plural, 2])}</a>";
    }
}

if ($links) {
    echo "
    <nav class=\"dropdown\">
        <button class=\"launcher\" id=\"adminMenu\"  aria-haspopup=\"true\" aria-expanded=\"false\">{$this->_('admin')}</button>
        <div class=\"links\" aria-labeledby=\"adminMenu\">$links</div>
    </nav>
    ";
}
