<?php
/**
 * @copyright 2012-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Person $this->person
*/
$username     = View::escape($this->person->getUsername());
$name         = View::escape($this->person->getFullname());
$organization = View::escape($this->person->getOrganization());
echo "
<person id=\"{$this->person->getId()}\">
	<fullname>$name</fullname>
	<organization>$organization</organization>
	<username>$username</username>
";
$list = $this->person->getEmails();
if (count($list)) {
	echo "<emails>";
	foreach ($list as $email) {
		$n = View::escape($email->getEmail());
		echo "<email>$n</email>";
	}
	echo "</emails>";
}
$list = $this->person->getPhones();
if (count($list)) {
	echo "<phones>";
	foreach ($list as $phone) {
		$n = View::escape($phone->getNumber());
		echo "<phone>$n</phone>";
	}
	echo "</phones>";
}
$list = $this->person->getAddresses();
if (count($list)) {
	echo "<addresses>";
	foreach ($list as $a) {
		foreach(array('address','city','state','zip') as $field) {
			$get = 'get'.ucfirst($field);
			$value = View::escape($a->$get());
			echo "<$field>$value</$field>";
		}
	}
	echo "</addresses>";
}
echo "
</person>
";