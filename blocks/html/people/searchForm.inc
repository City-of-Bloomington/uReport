<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param string $this->return_url
 */
?>
<div class="findPeople">
	<h1>Find Someone</h1>
	<form method="get" action="<?php echo BASE_URL; ?>/people">
		<fieldset>
			<table>
			<?php
				$fields = array('firstname','lastname','organization','email','address','phone');
				foreach ($fields as $field) {
					$label = ucfirst($field);
					$value = isset($_GET[$field]) ? View::escape($_GET[$field]) : '';
					echo "
					<tr><td><label for=\"$field\">$label</label></td>
						<td><input name=\"$field\" id=\"$field\" value=\"$value\" /></td>
					</tr>
					";
				}
			?>
			</table>
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
