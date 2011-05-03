<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Ticket $this->ticket
 */
if ($this->ticket->getAssignedPerson()) {
	$person = $this->ticket->getAssignedPerson();
	$username = View::escape($person['username']);
}
else {
	$username = View::escape($_SESSION['USER']->getUsername());
}
?>
<table>
	<tr><td><label for="assignedPerson">Person</label></td>
		<td><input name="assignedPerson" id="assignedPerson" value="<?php echo $username; ?>" /></td>
	</tr>
</table>
<div><label for="notes">Note to the assignee</label></div>
<div><textarea name="notes" id="notes" rows="3" cols="50"></textarea></div>
