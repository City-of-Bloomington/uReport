<?php
/**
 * @copyright 2013-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Phone $this->phone
 */
?>
<div class="updatePhoneForm">
	<form method="post">
		<?php $this->_include('people/phoneFields.inc'); ?>
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

				$helper = $this->template->getHelper('saveAndCancelButtons');
				echo $helper->saveAndCancelButtons($cancelUrl);
			?>
		</fieldset>
	</form>
</div>
