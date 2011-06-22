<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param string $this->return_url
 */
$return_url = isset($this->return_url) ? View::escape($this->return_url) : '';
$text = isset($_GET['location']['text']) ? View::escape($_GET['location']['text']) : '';
?>
<div class="findDescriptiveLocationForm">
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