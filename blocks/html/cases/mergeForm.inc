<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Case $this->caseA
 * @param Case $this->caseB
 */
?>
<div class="mergeForm">
	<form method="post" action="<?php echo BASE_URL; ?>/cases/mergeCases.php">
		<fieldset>
			<div class="target_a">
				<input name="targetCase" id="targetCase_a" type="radio" value="a" checked="checked" />
				<input name="case_id_a" type="hidden" value="<?php echo $this->caseA->getId(); ?>" />
				<label for="targetCase_a">Merge Into Case #<?php echo $this->caseA->getId(); ?></label>
			</div>
			<div class="target_b">
				<label for="targetCase_b">Merge Into Case #<?php echo $this->caseB->getId(); ?></label>
				<input name="targetCase" id="targetCase_b" type="radio" value="b" />
				<input name="case_id_b" type="hidden" value="<?php echo $this->caseB->getId(); ?>" />
			</div>
			<?php echo $this->formButton('Merge','submit','submit'); ?>
		</fieldset>
	</form>
</div>