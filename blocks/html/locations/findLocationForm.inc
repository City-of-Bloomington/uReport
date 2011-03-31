<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param string $this->return_url (optional)
 */
$return_url = isset($this->return_url) ? View::escape($this->return_url) : '';

$address = isset($_GET['location']['address']) ? View::escape($_GET['location']['address']) : '';
$street = isset($_GET['location']['street']) ? View::escape($_GET['location']['street']) : '';
$text = isset($_GET['location']['text']) ? View::escape($_GET['location']['text']) : '';
$subunit_identifier = isset($_GET['location']['subunit_identifier'])
	? View::escape($_GET['location']['subunit_identifier'])
	: '';
?>
<div class="findLocationForm">
	<h1>Find a Location</h1>
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
	<form method="get" action="<?php echo BASE_URL; ?>/locations">
		<fieldset><legend>Find a street</legend>
			<input name="location[street]" id="location-street" value="<?php echo $street; ?>" />
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
	<form method="get" action="<?php echo BASE_URL; ?>/locations">
		<fieldset><legend>Find a descriptive location</legend>
			<input name="location[text]" id="location-text" value="<?php echo $text; ?>" />
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