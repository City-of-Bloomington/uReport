<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Person $this->personA
 * @param Person $this->personB
 */
?>
<div class="mergeForm">
	<form method="post" action="<?php echo BASE_URL; ?>/people/mergePeople.php">
		<fieldset>
			<div class="target_a">
				<input name="targetPerson" id="targetPerson_a" type="radio" value="a" checked="checked" />
				<input name="person_id_a" type="hidden" value="<?php echo $this->personA->getId(); ?>" />
				<label for="targetPerson_a">Merge Into Person #<?php echo $this->personA->getId(); ?></label>
			</div>
			<div class="target_b">
				<label for="targetPerson_b">Merge Into Person #<?php echo $this->personB->getId(); ?></label>
				<input name="targetPerson" id="targetPerson_b" type="radio" value="b" />
				<input name="person_id_b" type="hidden" value="<?php echo $this->personB->getId(); ?>" />
			</div>
			<?php echo $this->formButton('Merge','submit','submit'); ?>
		</fieldset>
	</form>
</div>