<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param ConstituentList $this->constituentList
 */
echo "
<table>
	<thead>
		<tr><th>First Name</th>
			<th>Last Name</th>
			<th>Email</th>
			<th>Phone</th>
			<th>Address</th>
		</tr>
	</thead>
	<tbody>
";
	foreach ($this->constituentList as $constituent) {
		$firstname = View::escape($constituent->getFirstname());
		$lastname = View::escape($constituent->getLastname());
		$email = View::escape($constituent->getEmail());
		$address = View::escape($constituent->getAddress());
		echo "
		<tr><td>$firstname</td>
			<td>$lastname</td>
			<td>$email</td>
			<td></td>
			<td>$address</td>
		</tr>
		";
	}
echo "
	</tbody>
</table>
";