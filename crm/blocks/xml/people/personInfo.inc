<?php
/**
 * @copyright 2012-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Person $this->person
*/
$username     = View::escape($this->person->getUsername());
$name         = View::escape($this->person->getFullname());
$email        = View::escape($this->person->getEmail());
$organization = View::escape($this->person->getOrganization());
$address      = View::escape($this->person->getAddress());
$city         = View::escape($this->person->getCity());
$state        = View::escape($this->person->getState());
$zip          = View::escape($this->person->getZip());
echo "
<person id=\"{$this->person->getId()}\">
	<fullname>$name</fullname>
	<organization>$organization</organization>
	<username>$username</username>
	<email>$email</email>
	<address>$address</address>
	<city>$city</city>
	<state>$state</state>
	<zip>$zip</zip>
";
$list = $this->person->getPhones();
if (count($list)) {
	echo "<phones>";
	foreach ($list as $phone) {
		$n = View::escape($phone->getNumber());
		echo "<phone>$n</phone>";
	}
	echo "</phones>";
}
echo "
</person>
";