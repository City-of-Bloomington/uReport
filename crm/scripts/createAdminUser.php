<?php
/**
 * Use this script to create an initial Administrator account.
 *
 * This script should only be used by people with root access
 * on the web server.

 * If you are planning on using local authentication, you must
 * provide a password here.  The password will be encrypted when
 * the new person's account is saved
 *
 * If you are doing Employee or CAS authentication you do
 * not need to save a password into the database.
 *
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
include '../configuration.inc';

$person = new Person();

// Fill these out as needed
$person->setFirstname('Cliff');
$person->setLastname('Ingham');
$person->setEmail('inghamn@bloomington.in.gov');
$person->setUsername('inghamn');
$person->setAuthenticationMethod('Employee');
$person->setPassword('');

// No more changes needed
$person->setRole('Administrator');
$person->save();
