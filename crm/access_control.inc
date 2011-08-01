<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$ZEND_ACL = new Zend_Acl();
/**
 * Load the roles from the database
 */
$roles = array('Administrator','Staff');
foreach ($roles as $role) {
	$ZEND_ACL = $ZEND_ACL->addRole(new Zend_Acl_Role($role));
}

/**
 * Declare all the resources
 */
$ZEND_ACL->add(new Zend_Acl_Resource('Users'));
$ZEND_ACL->add(new Zend_Acl_Resource('People'));
$ZEND_ACL->add(new Zend_Acl_Resource('Departments'));

$ZEND_ACL->add(new Zend_Acl_Resource('Actions'));
$ZEND_ACL->add(new Zend_Acl_Resource('Categories'));
$ZEND_ACL->add(new Zend_Acl_Resource('IssueTypes'));
$ZEND_ACL->add(new Zend_Acl_Resource('Resolutions'));
$ZEND_ACL->add(new Zend_Acl_Resource('ContactMethods'));
$ZEND_ACL->add(new Zend_Acl_Resource('NeighborhoodAssociations'));

$ZEND_ACL->add(new Zend_Acl_Resource('Tickets'));
$ZEND_ACL->add(new Zend_Acl_Resource('Issues'));
/**
 * Assign permissions to the resources
 */
// Administrator is allowed access to everything
$ZEND_ACL->allow('Administrator');

// Staff Permissions
$ZEND_ACL->allow('Staff','Tickets');
$ZEND_ACL->allow('Staff','People');
$ZEND_ACL->allow('Staff','Issues');
