<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param string $this->return_url
 */
$return_url = isset($this->return_url) ? View::escape($this->return_url) : '';
$address = isset($_GET['location']['address']) ? View::escape($_GET['location']['address']) : '';
?>
<div class="findAddressForm">
	<form method="get" action="<?php echo BASE_URL; ?>/locations">
		<fieldset><legend>Find a specific address</legend>
			<input name="location[address]" id="location-address" value="<?php echo $address; ?>" />
			<?php
				if ($return_url) {
					echo "<input type=\"hidden\" name=\"return_url\" value=\"$return_url\" />";
				}
				echo $this->template->formButton(
					'Search','submit','submit'
				);
			?>
		</fieldset>
	</form>
</div>
