<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$methods = Lookups::get('contactMethods');
$methods = View::escape(implode(', ',$methods));
?>
<div class="updateLabelsForm">
	<h1>Edit Contact Methods</h1>
	<p>
		Type the Contact Methods you want to use for issues, seperated by commas.
		These will show up on the issue editing screen and on the ticket search form.
	</p>
	<form method="post" action="<?php echo BASE_URL; ?>/lookups/updateContactMethods.php">
		<fieldset>
			<div><label for="contactMethods">Contact Methods</label></div>
			<div><textarea name="contactMethods" id="contactMethods"><?php echo $methods; ?></textarea></div>
			<div>
			<?php
				echo $this->template->formButton('Submit','submit','submit');
				echo $this->template->formButton(
					'Cancel','button','cancel',null,"document.location.href='".BASE_URL."/lookups/contactMethods.php';"
				);
			?>
			</div>
		</fieldset>
	</form>
</div>