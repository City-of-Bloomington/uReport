<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Issue $this->issue
 */
$title = $this->issue->getId() ? 'Edit Issue' : 'Add Issue';
?>
<h1><?php echo $title; ?></h1>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
	<fieldset><legend>Issue Info</legend>
		<input name="issue_id" type="hidden" value="<?php echo $this->issue->getId(); ?>" />
		<input name="ticket_id" type="hidden" value="<?php echo $this->issue->getTicket_id(); ?>" />
		<?php
			include APPLICATION_HOME.'/blocks/html/tickets/issueFields.inc';

			echo $this->template->formButton('Submit','submit','submit');
			echo $this->template->formButton(
				'Cancel','button','cancel',null,"document.location.href='{$this->issue->getTicket()->getURL()}';"
			);
		?>
	</fieldset>
</form>