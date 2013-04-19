<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Phone $this->phone
 */
?>
<div class="updatePhoneForm">
	<form method="post">
		<?php include APPLICATION_HOME.'/blocks/html/people/phoneFields.inc'; ?>
		<fieldset>
			<?php
				if ($this->phone->getId()) {
					echo "<input type=\"hidden\" name=\"phone_id\" value=\"{$this->phone->getId()}\" />";
				}
				else {
					echo "<input type=\"hidden\" name=\"person_id\" value=\"{$this->phone->getPerson_id()}\" />";
				}
				$cancelUrl = $this->template->controller == 'AccountController'
					? BASE_URI.'/account'
					: $this->phone->getPerson()->getUrl();
			?>
			<button type="submit"><span class="icon-save"></span> Save</button>
			<a class="btn" href="<?php echo $cancelUrl; ?>">
				<span class="icon-remove"></span>
				Cancel
			</a>
		</fieldset>
	</form>
</div>
