<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param string $this->title
 * @param string $this->return_url
 */
?>
<div class="confirm">
	<h1><?php echo $this->title ? View::escape($this->title) : 'Confirm'; ?></h1>
	<form method="get" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
		<fieldset><legend>Are you sure?</legend>
			<input name="confirm" type="hidden" value="1" />
			<?php
				foreach ($_GET as $key=>$value) {
					$value = View::escape($value);
					echo "<input name=\"$key\" type=\"hidden\" value=\"$value\" />";
				}
				echo $this->template->formButton('Yes','submit','submit');
				echo $this->template->formButton(
					'Cancel','button','cancel',null,"document.location.href='{$this->return_url}';"
				);
			?>
		</fieldset>
	</form>
</div>
