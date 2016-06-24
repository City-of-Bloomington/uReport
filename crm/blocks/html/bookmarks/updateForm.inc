<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param string $this->type
 */
use Blossom\Classes\View;

$type = self::escape($this->type);
?>
<form id="bookmark-updateForm" method="post" action="<?= BASE_URI; ?>/bookmarks/update">
	<fieldset>
		<input type="hidden" name="type" value="<?= $type; ?>" />
		<input type="hidden" name="requestUri" value="<?= $_SERVER['REQUEST_URI']; ?>" />
		<label>
			<?php
				echo sprintf($this->_('save_as'), $type);
			?>:<input name="name" />
		</label>
		<button type="submit"><span class="fa fa-floppy-o"></span> <?= $this->_('save'  ); ?></button>
		<button type="reset"> <span class="fa fa-times"></span> <?= $this->_('cancel'); ?></button>
	</fieldset>
</form>
