<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$ZEND_ACL = new Zend_Acl();
$ZEND_ACL->addRole(new Zend_Acl_Role('Anonymous'))
		 ->addRole(new Zend_Acl_Role('Public'), 'Anonymous')
		 ->addRole(new Zend_Acl_Role('Staff'), 'Public')
		 ->addRole(new Zend_Acl_Role('Administrator'), 'Staff');

/**
 * Declare all the resources
 */
$ZEND_ACL->add(new Zend_Acl_Resource('index'));
$ZEND_ACL->add(new Zend_Acl_Resource('callback'));
$ZEND_ACL->add(new Zend_Acl_Resource('login'));
$ZEND_ACL->add(new Zend_Acl_Resource('admin'));
$ZEND_ACL->add(new Zend_Acl_Resource('account'));

$ZEND_ACL->add(new Zend_Acl_Resource('users'));
$ZEND_ACL->add(new Zend_Acl_Resource('people'));
$ZEND_ACL->add(new Zend_Acl_Resource('departments'));

$ZEND_ACL->add(new Zend_Acl_Resource('actions'));
$ZEND_ACL->add(new Zend_Acl_Resource('categories'));
$ZEND_ACL->add(new Zend_Acl_Resource('categoryGroups'));
$ZEND_ACL->add(new Zend_Acl_Resource('labels'));
$ZEND_ACL->add(new Zend_Acl_Resource('issueTypes'));
$ZEND_ACL->add(new Zend_Acl_Resource('substatus'));
$ZEND_ACL->add(new Zend_Acl_Resource('contactMethods'));
$ZEND_ACL->add(new Zend_Acl_Resource('neighborhoodAssociations'));

$ZEND_ACL->add(new Zend_Acl_Resource('locations'));
$ZEND_ACL->add(new Zend_Acl_Resource('tickets'));
$ZEND_ACL->add(new Zend_Acl_Resource('issues'));
$ZEND_ACL->add(new Zend_Acl_Resource('media'));

$ZEND_ACL->add(new Zend_Acl_Resource('open311'));
$ZEND_ACL->add(new Zend_Acl_Resource('clients'));
$ZEND_ACL->add(new Zend_Acl_Resource('reports'));
$ZEND_ACL->add(new Zend_Acl_Resource('bookmarks'));

// Permissions for non-authenticated web browsing
$ZEND_ACL->allow(null,array('callback','login','open311'));
$ZEND_ACL->allow(null,
				array('index','tickets','issues','locations'),
				array('index','view', 'thumbnails'));
$ZEND_ACL->allow(null, 'media', 'resize');

// Staff permission
// Staff has full permission to these tables
$ZEND_ACL->allow('Staff',array(
	'account','people','issues','media','reports','bookmarks'
));
// Staff has limited permission to these tables
$ZEND_ACL->allow('Staff','departments',array('index', 'view', 'choose'));
$ZEND_ACL->allow('Staff','categories', array('index', 'view', 'choose'));
$ZEND_ACL->allow('Staff','substatus',  array('index'));
$ZEND_ACL->allow('Staff','tickets', array(
	'add', 'update', 'merge', 'delete','assign', 'refer',
	'changeCategory', 'changeLocation', 'changeStatus'
));


// Administrator is allowed access to everything
$ZEND_ACL->allow('Administrator');
