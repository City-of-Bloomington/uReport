<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$methods = Lookups::get('contactMethods');
$methods = View::escape(implode(', ',$methods));
?>
<div class="updateContactMethodsForm">
	<h1>Edit Contact Methods</h1>
	<p>
		Type the Contact Methods you want to use for issues, seperated by commas.
		These will show up on the issue editing screen and on the ticket search form.
	</p>
	<form method="post" action="<?php echo BASE_URI; ?>/contactMethods/update">
		<fieldset>
			<div><label for="contactMethods">Contact Methods</label></div>
			<div><textarea name="contactMethods" id="contactMethods"><?php echo $methods; ?></textarea></div>
			<div>
				<button type="submit" class="submit">Submit</button>
				<button type="button" class="cancel"
					onclick="document.location.href='<?php echo BASE_URI; ?>/contactMethods';">
					Cancel
				</button>
			</div>
		</fieldset>
	</form>
</div>