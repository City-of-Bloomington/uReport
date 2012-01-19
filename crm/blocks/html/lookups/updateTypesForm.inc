<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$types = Lookups::get('types');
$types = View::escape(implode(', ',$types));
?>
<div class="updateLabelsForm">
	<h1>Edit Issue Types</h1>
	<p>
		Enter the issue types you want to use, seperated by commas.
		These will show up on the issue editing screen and on the ticket search form.
	</p>
	<form method="post" action="<?php echo BASE_URL; ?>/lookups/updateTypes.php">
		<fieldset>
			<div><label for="labels">Issue Types</label></div>
			<div><textarea name="types" id="types"><?php echo $types; ?></textarea></div>
			<div>
				<button type="submit" class="submit">Submit</button>
				<button type="button" class="cancel"
					onclick="document.location.href='<?php echo BASE_URL; ?>/lookups/types.php';">
					Cancel
				</button>
			</div>
		</fieldset>
	</form>
</div>