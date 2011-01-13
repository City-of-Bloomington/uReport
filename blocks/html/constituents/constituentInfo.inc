<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Constituent $this->constituent
 */
$fields = array('firstname','lastname','middlename','salutation','address','city','state','zip','email');
foreach ($fields as $field) {
	$getField = 'get'.ucfirst($field);
	$$field = View::escape($this->constituent->$getField());
}

if (userIsAllowed('Constituents')) {
	$editButton = $this->template->linkButton(
		'Edit',
		BASE_URL.'/constituents/updateConstituent.php?constituent_id='.$this->constituent->getId(),
		'edit'
	);
}
echo "
<h1>{$editButton}Constituent Information</h1>
<table>
	<tr><th>Salutation</th><td>$salutation</td></tr>
	<tr><th>First Name</th><td>$firstname</td></tr>
	<tr><th>Middle Name</th><td>$middlename</td></tr>
	<tr><th>Last Name</th><td>$lastname</td></tr>
	<tr><th>Email Address</th><td>$email</td></tr>
	<tr><th>Phone</th><td></td></tr>
	<tr><th>Address</th><td>$address</td></tr>
	<tr><th>City State Zip</th><td>$city $state $zip</td></tr>
</table>
";