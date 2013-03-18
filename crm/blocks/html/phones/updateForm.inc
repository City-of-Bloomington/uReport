<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Phone $this->phone
 */
?>
<form method="post" action="<?php echo BASE_URI; ?>/phones/update">
	<?php include APPLICATION_HOME.'/blocks/html/phones/fields.inc'; ?>
	<fieldset>
		<?php
			if ($this->phone->getId()) {
				echo "<input type=\"hidden\" name=\"phone_id\" value=\"{$this->phone->getId()}\" />";
			}
			else {
				echo "<input type=\"hidden\" name=\"person_id\" value=\"{$this->phone->getPerson_id()}\" />";
			}
		?>
		<button type="submit" class="save">Save</button>
		<a class="cancel" href="<?php echo $this->phone->getPerson()->getURL(); ?>">Cancel</a>
	</fieldset>
</form>