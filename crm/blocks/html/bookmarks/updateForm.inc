<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param string $this->type
 */
use Blossom\Classes\View;

$type = View::escape($this->type);
?>
<form id="bookmark-updateForm" method="post" action="<?php echo BASE_URI; ?>/bookmarks/update">
	<fieldset>
		<input type="hidden" name="type" value="<?php echo $type; ?>" />
		<input type="hidden" name="requestUri" value="<?php echo $_SERVER['REQUEST_URI']; ?>" />
		<label>
			<?php
				echo sprintf($this->translate('labels.save_as'), $type);
			?>:<input name="name" />
		</label>
		<button type="submit"><span class="icon-save">  </span> <?php echo $this->translate('labels.save'  ); ?></button>
		<button type="reset"> <span class="icon-remove"></span> <?php echo $this->translate('labels.cancel'); ?></button>
	</fieldset>
</form>
