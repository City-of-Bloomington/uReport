<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>, W. Sibo <sibow@bloomington.in.gov>
 * @param Case $this->case
 */
?>
<div class="assignCaseForm">
	<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
		<fieldset><legend>Assign Case</legend>
			<input name="case_id" type="hidden" value="<?php echo $this->case->getId(); ?>" />
			<?php
				include APPLICATION_HOME.'/blocks/html/cases/assignCaseFields.inc';

				echo $this->template->formButton('Submit','submit','submit');
				echo $this->template->formButton(
					'Cancel','button','cancel',null,"document.location.href='{$this->case->getURL()}';"
				);
			?>
		</fieldset>
	</form>
</div>
