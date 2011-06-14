<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param string $this->return_url
 */
$return_url = isset($this->return_url) ? View::escape($this->return_url) : '';
$street = isset($_GET['location']['street']) ? View::escape($_GET['location']['street']) : '';
?>
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
