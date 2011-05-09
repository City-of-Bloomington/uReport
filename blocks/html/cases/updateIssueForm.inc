<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Case $this->case
 * @param Issue $this->issue
 * @param int $this->index
 */
$title = isset($this->index) ? 'Edit Issue' : 'Add Issue';
?>
<h1><?php echo $title; ?></h1>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
	<fieldset><legend>Issue Info</legend>
		<input name="case_id" type="hidden" value="<?php echo $this->case->getId(); ?>" />
		<input name="index" type="hidden" value="<?php echo $this->index; ?>" />
		<?php
			include APPLICATION_HOME.'/blocks/html/cases/issueFields.inc';

			echo $this->template->formButton('Submit','submit','submit');
			echo $this->template->formButton(
				'Cancel','button','cancel',null,"document.location.href='{$this->case->getURL()}';"
			);
		?>
	</fieldset>
</form>