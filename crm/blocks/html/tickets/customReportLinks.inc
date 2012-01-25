<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
?>
<div class="customReportLinks">
	<form method="get" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
		<fieldset><legend>Search Results Display Options</legend>
			<label>Display as:
				<select name="report">
					<option value="">Standard</option>
					<?php
						$reports = glob(APPLICATION_HOME.'/blocks/html/tickets/reports/*.inc');
						if (count($reports)) {
							foreach ($reports as $filename) {
								$report = str_replace('.inc','',basename($filename));
								$name = str_replace('_',' ',$report);
								$selected = (isset($_GET['report']) && $report==$_GET['report'])
									? 'selected="selected"'
									: '';
								echo "<option value=\"$report\" $selected>$name</option>";
							}
						}
					?>
				</select>
			</label>
			<?php
				// We need to preserve all the stuff they already searched for,
				// Render out all the search parameters as hidden inputs
				foreach ($_GET as $key=>$value) {
					if ($key!='report' && $value) {
						if (is_array($value)) {
							foreach ($value as $i=>$v) {
								$v = View::escape($v);
								echo "<input type=\"hidden\" name=\"{$key}[$i]\" value=\"$v\" />";
							}
						}
						else {
							$value = View::escape($value);
							echo "<input type=\"hidden\" name=\"$key\" value=\"$value\" />";
						}
					}
				}
			?>
			<button type="submit" class="search">Display</button>
		</fieldset>
	</form>
</div>
