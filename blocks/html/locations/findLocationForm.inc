<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param bool $this->includeExternalResults (optional)
 * @param string $this->return_url (optional)
 */
$address = isset($_GET['location']['address']) ? View::escape($_GET['location']['address']) : '';
$street = isset($_GET['location']['street']) ? View::escape($_GET['location']['street']) : '';
$text = isset($_GET['location']['text']) ? View::escape($_GET['location']['text']) : '';
?>
<div class="findLocationForm">
	<h1>Find a Location</h1>
	<form method="get" action="<?php echo BASE_URL; ?>/locations">
		<fieldset>
			<div>
				<label>
					Address
					<input name="location[address]" value="<?php echo $address; ?>" />
				</label>
			</div>
			<div>
				<label>
					Street
					<input name="location[street]" value="<?php echo $street; ?>" />
				</label>
			</div>
			<div>
				<label>
					Text
					<input name="location[text]" value="<?php echo $text; ?>" />
				</label>
			</div>
			<?php
				$return_url = isset($this->return_url) ? View::escape($this->return_url) : '';
				if ($return_url) {
					echo "<input type=\"hidden\" name=\"return_url\" value=\"$return_url\" />";
				}

				if ($this->includeExternalResults) {
					echo "<input type=\"hidden\" name=\"includeExternalResults\" value=\"1\" />";
				}

				echo $this->template->formButton(
					'Search','submit','submit'
				);
			?>
		</fieldset>
	</form>
</div>