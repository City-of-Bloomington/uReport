<?php
/**
 * @copyright 2011-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$address = isset($_GET['location']['address']) ? View::escape($_GET['location']['address']) : '';
?>
<div class="findAddressForm">
	<form method="get" action="<?php echo BASE_URL; ?>/locations">
		<fieldset><legend>Find a specific address</legend>
			<input name="location[address]" id="location-address" value="<?php echo $address; ?>" />
			<?php
				// Preserve any extra parameters passed in
				foreach ($_REQUEST as $key=>$value) {
					if ($key != 'location') {
						$key = View::escape($key);
						$value = View::escape($value);
						echo "<input type=\"hidden\" name=\"$key\" value=\"$value\" />\n";
					}
				}
			?>
			<button type="submit"><span class="icon-search"></span> Search</button>
		</fieldset>
	</form>
</div>
