<?php
/**
 * @copyright 2012-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
$_SERVER['SITE_HOME'] = __DIR__;
include '../bootstrap.inc';

// These are the basic actions that all things should have
$actions = array(null,'index','view','add','update','delete');

foreach ($ZEND_ACL->getRoles() as $role) {
	echo "--------------------------\n$role\n--------------------------\n";
	foreach ($ZEND_ACL->getResources() as $resource) {
		foreach ($actions as $a) {
			echo sprintf('%-40s',"$resource:$a");
			echo $ZEND_ACL->isAllowed($role, $resource, $a)
				? "allowed\n"
				: "not allowed\n";
		}
	}
}
