<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param bool $this->includeExternalResults (optional)
 * @param string $this->return_url (optional)
 */
$location_query = isset($_GET['location_query']) ? View::escape($_GET['location_query']) : '';
?>
<div class="findLocationForm">
	<h1>Find a Location</h1>
	<form method="get" action="<?php echo BASE_URL; ?>/locations">
		<fieldset>
			<input name="location_query" value="<?php echo $location_query; ?>" />
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