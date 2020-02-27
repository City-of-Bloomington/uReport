<?php
/**
 * @copyright 2013-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param string $this->type
 */
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
		<button type="submit" class="save"><?= $this->_('save'  ); ?></button>
		<button type="reset"  class="cancel"><?= $this->_('cancel'); ?></button>
	</fieldset>
</form>
