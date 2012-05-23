<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
require_once './config.inc';
// Clear out the lookup tables.  We'll import everything from Mongo
// The mysql.sql script preloads some generic values for these tables
$zend_db->delete('resolutions');
$zend_db->delete('actions');

$result = $mongo->resolutions->find();
foreach ($result as $r) {
	$o = new Resolution();
	$o->handleUpdate($r);
	$o->save();
	echo "Resolution: {$o->getName()}\n";
}

$result = $mongo->actions->find();
foreach ($result as $r) {
	$o = new Action();
	$o->handleUpdate($r);
	$o->save();
	echo "Action: {$o->getName()}\n";
}

$result = $mongo->lookups->findOne(array('name'=>'contactMethods'));
$methods = $result['items'];
foreach ($methods as $m) {
	$o = new ContactMethod();
	$o->setName($m);
	$o->save();
	echo "ContactMethod: {$o->getName()}\n";
}

$result = $mongo->lookups->findOne(array('name'=>'types'));
$types = $result['items'];
foreach ($types as $t) {
	$o = new IssueType();
	$o->setName($t);
	$o->save();
	echo "IssueType: {$o->getName()}\n";
}

$result = $mongo->lookups->findOne(array('name'=>'labels'));
$labels = $result['items'];
foreach ($labels as $l) {
	$o = new Label();
	$o->setName($l);
	$o->save();
	echo "Label: {$o->getName()}\n";
}

$result = $mongo->categoryGroups->find();
foreach ($result as $r) {
	$o = new CategoryGroup();
	$o->setName($r['name']);
	$o->setOrdering($r['order']);
	$o->save();
	echo "CategoryGroup: {$o->getName()}\n";
}
