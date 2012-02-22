<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Ticket $this->ticket
 */
?>
<button type="submit" class="submit">Submit</button>
<?php
	$onclick = isset($_REQUEST['callback'])
		? 'self.close();'
		: "document.location.href='{$this->ticket->getURL()}';";
?>
<button type="button" class="cancel" onclick="<?php echo $onclick; ?>">Cancel</button>
