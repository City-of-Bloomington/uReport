<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param string $this->return_url
 */
$personQuery = isset($_GET['personQuery']) ? View::escape($_GET['personQuery']) : '';
?>
<div class="findPeople">
	<h1>Find Someone</h1>
	<form method="get" action="<?php echo BASE_URL; ?>/people">
		<fieldset>
			<input name="personQuery" value="<?php echo $personQuery; ?>" />
			<?php
				echo $this->template->formButton(
					'Search','submit','search'
				);
				if (isset($this->return_url)) {
					$return_url = View::escape($this->return_url);
					echo "<input type=\"hidden\" name=\"return_url\" value=\"$return_url\" />";

					echo $this->template->formButton(
						'Cancel','button','cancel',null,"document.location.href='{$return_url}';"
					);
				}

			?>
		</fieldset>
	</form>
</div>
