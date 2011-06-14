<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
include '../configuration.inc';

$mongo = Database::getConnection();

$person = new Person();
$person->setFirstname('Admin');
$person->setLastname('Person');
$person->setEmail('');
$person->setUsername('administrator');
$person->setAuthenticationMethod('local');
$person->setPassword('');
$person->setRoles(array('Administrator'));
$person->save();
