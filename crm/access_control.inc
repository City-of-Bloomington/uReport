<?php
/**
 * @copyright 2011-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
use Laminas\Permissions\Acl\Acl;
use Laminas\Permissions\Acl\Role\GenericRole as Role;
use Laminas\Permissions\Acl\Resource\GenericResource as Resource;

$ACL = new Acl();
$ACL->addRole(new Role('Anonymous'))
    ->addRole(new Role('Public'), 'Anonymous')
    ->addRole(new Role('Staff'), 'Public')
    ->addRole(new Role('Administrator'), 'Staff');

/**
 * Declare all the controllers as resources
 */
$ACL->addResource(new Resource('index'));
$ACL->addResource(new Resource('callback'));
$ACL->addResource(new Resource('login'));
$ACL->addResource(new Resource('account'));

$ACL->addResource(new Resource('users'));
$ACL->addResource(new Resource('people'));
$ACL->addResource(new Resource('departments'));

$ACL->addResource(new Resource('actions'));
$ACL->addResource(new Resource('categories'));
$ACL->addResource(new Resource('categoryGroups'));
$ACL->addResource(new Resource('responseTemplates'));
$ACL->addResource(new Resource('issueTypes'));
$ACL->addResource(new Resource('substatus'));
$ACL->addResource(new Resource('contactMethods'));
$ACL->addResource(new Resource('neighborhoodAssociations'));

$ACL->addResource(new Resource('locations'));
$ACL->addResource(new Resource('tickets'));
$ACL->addResource(new Resource('media'));
$ACL->addResource(new Resource('solr'));

$ACL->addResource(new Resource('open311'));
$ACL->addResource(new Resource('clients'));
$ACL->addResource(new Resource('reports'));
$ACL->addResource(new Resource('metrics'));
$ACL->addResource(new Resource('bookmarks'));

// Permissions for non-authenticated web browsing
$ACL->allow(null,['callback', 'login', 'open311', 'metrics']);
$ACL->allow(null, ['index','tickets','locations'], ['index','view', 'thumbnails']);
$ACL->allow(null, 'media', 'resize');
$ACL->allow(null, 'solr', 'index');

// Staff permission
// Staff has full permission to these controllers
$ACL->allow('Staff', [
	'account','people','media','reports','bookmarks'
]);

// Staff has limited permission to these controllers
$ACL->allow('Staff','departments',['index', 'view', 'choose']);
$ACL->allow('Staff','categories', ['index', 'view']);
$ACL->allow('Staff','substatus',  ['index']);
$ACL->allow('Staff','tickets', [
	'add', 'update', 'merge', 'delete','assign', 'print', 'respond', 'message',
	'changeCategory', 'changeLocation', 'open', 'close', 'recordAction'
]);


// Administrator is allowed access to everything
$ACL->allow('Administrator');
