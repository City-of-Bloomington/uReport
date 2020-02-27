<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param string $this->title
 * @param string $this->return_url
 */
use Blossom\Classes\View;
?>
<div class="confirm">
	<h1><?= $this->title ? self::escape($this->title) : 'Confirm'; ?></h1>
	<form method="get" action="<?= $_SERVER['REDIRECT_URL']; ?>">
		<fieldset><legend>Are you sure?</legend>
			<input name="confirm" type="hidden" value="1" />
			<?php
				foreach ($_GET as $key=>$value) {
					$value = self::escape($value);
					echo "<input name=\"$key\" type=\"hidden\" value=\"$value\" />";
				}
			?>
			<button type="submit" class="submit">Submit</button>
			<a class="cancel" href="<?= $this->return_url; ?>">Cancel</a>
		</fieldset>
	</form>
</div>
