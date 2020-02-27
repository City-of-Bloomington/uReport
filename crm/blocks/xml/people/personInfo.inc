<?php
/**
 * @copyright 2012-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Person $this->person
*/
use Blossom\Classes\View;

$username     = self::escape($this->person->getUsername());
$name         = self::escape($this->person->getFullname());
$organization = self::escape($this->person->getOrganization());
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
		$n = self::escape($email->getEmail());
		echo "<email>$n</email>";
	}
	echo "</emails>";
}
$list = $this->person->getPhones();
if (count($list)) {
	echo "<phones>";
	foreach ($list as $phone) {
		$n = self::escape($phone->getNumber());
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
			$value = self::escape($a->$get());
			echo "<$field>$value</$field>";
		}
	}
	echo "</addresses>";
}
echo "
</person>
";
