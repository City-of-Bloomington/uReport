<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Ticket $this->ticket
 */
?>
<h1>Thank You</h1>
<h2>Case #<?php echo $this->ticket->getNumber(); ?></h2>
<p>Your report has been received and assigned to a staff member.</p>
<p>You can follow progess on this report at
<?php
	echo "<a href=\"{$this->ticket->getURL()}\">{$this->ticket->getURL()}</a>";
?>
</p>
