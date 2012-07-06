<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Ticket $this->ticket
 */
$onclick = isset($_REQUEST['callback'])
	? 'onclick="self.close();"'
	: '';
echo "
<button type=\"submit\" class=\"submit\">Submit</button>
<a class=\"cancel\" href=\"{$this->ticket->getURL()}\" $onclick>Cancel</a>
";
