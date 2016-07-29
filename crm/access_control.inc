<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Role\GenericRole as Role;
use Zend\Permissions\Acl\Resource\GenericResource as Resource;

$ZEND_ACL = new Acl();
$ZEND_ACL->addRole(new Role('Anonymous'))
		 ->addRole(new Role('Public'), 'Anonymous')
		 ->addRole(new Role('Staff'), 'Public')
		 ->addRole(new Role('Administrator'), 'Staff');

/**
 * Declare all the controllers as resources
 */
$ZEND_ACL->addResource(new Resource('index'));
$ZEND_ACL->addResource(new Resource('callback'));
$ZEND_ACL->addResource(new Resource('login'));
$ZEND_ACL->addResource(new Resource('admin'));
$ZEND_ACL->addResource(new Resource('account'));

$ZEND_ACL->addResource(new Resource('users'));
$ZEND_ACL->addResource(new Resource('people'));
$ZEND_ACL->addResource(new Resource('departments'));

$ZEND_ACL->addResource(new Resource('actions'));
$ZEND_ACL->addResource(new Resource('categories'));
$ZEND_ACL->addResource(new Resource('categoryGroups'));
$ZEND_ACL->addResource(new Resource('responseTemplates'));
$ZEND_ACL->addResource(new Resource('issueTypes'));
$ZEND_ACL->addResource(new Resource('substatus'));
$ZEND_ACL->addResource(new Resource('contactMethods'));
$ZEND_ACL->addResource(new Resource('neighborhoodAssociations'));

$ZEND_ACL->addResource(new Resource('locations'));
$ZEND_ACL->addResource(new Resource('tickets'));
$ZEND_ACL->addResource(new Resource('issues'));
$ZEND_ACL->addResource(new Resource('media'));
$ZEND_ACL->addResource(new Resource('solr'));

$ZEND_ACL->addResource(new Resource('open311'));
$ZEND_ACL->addResource(new Resource('clients'));
$ZEND_ACL->addResource(new Resource('reports'));
$ZEND_ACL->addResource(new Resource('bookmarks'));

// Permissions for non-authenticated web browsing
$ZEND_ACL->allow(null,['callback','login','open311']);
$ZEND_ACL->allow(null,
				['index','tickets','issues','locations'],
				['index','view', 'thumbnails']);
$ZEND_ACL->allow(null, 'media', 'resize');
$ZEND_ACL->allow(null, 'solr', 'index');

// Staff permission
// Staff has full permission to these controllers
$ZEND_ACL->allow('Staff', [
	'account','people','issues','media','reports','bookmarks'
]);
// Staff has limited permission to these controllers
$ZEND_ACL->allow('Staff','departments',['index', 'view', 'choose']);
$ZEND_ACL->allow('Staff','categories', ['index', 'view', 'choose']);
$ZEND_ACL->allow('Staff','substatus',  ['index']);
$ZEND_ACL->allow('Staff','tickets', [
	'add', 'update', 'merge', 'delete','assign', 'print', 'respond',
	'changeCategory', 'changeLocation', 'changeStatus', 'recordAction'
]);


// Administrator is allowed access to everything
$ZEND_ACL->allow('Administrator');
