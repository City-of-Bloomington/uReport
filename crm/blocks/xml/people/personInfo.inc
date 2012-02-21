<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Person $this->person
*/
$username = View::escape($this->person->getUsername());
$name = View::escape($this->person->getFullname());
$phone = View::escape($this->person->getPhoneNumber());
$email = View::escape($this->person->getEmail());
$organization = View::escape($this->person->getOrganization());
$address = View::escape($this->person->getAddress());
$city = View::escape($this->person->getCity());
$state = View::escape($this->person->getState());
$zip = View::escape($this->person->getZip());
echo "
<person id=\"{$this->person->getId()}\">
	<fullname>$name</fullname>
	<organization>$organization</organization>
	<username>$username</username>
	<phone>$phone</phone>
	<email>$email</email>
	<address>$address</address>
	<city>$city</city>
	<state>$state</state>
	<zip>$zip</zip>
</person>
";
