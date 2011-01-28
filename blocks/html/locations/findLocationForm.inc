<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
?>
<h1>Find a Location</h1>
<form method="get" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
	<fieldset>
		<input name="location_query"
			value="<?php echo isset($_GET['location_query']) ? View::escape($_GET['location_query']) : ''; ?>" />
		<?php
			echo $this->template->formButton(
				'Search','submit','submit'
			);
		?>
	</fieldset>
</form>
