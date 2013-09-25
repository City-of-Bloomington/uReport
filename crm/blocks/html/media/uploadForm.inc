<?php
/**
 * @copyright 2011-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Issue $this->issue
 */
?>
<div class="uploadForm">
	<form method="post" action="<?php echo BASE_URL; ?>/media/upload" enctype="multipart/form-data">
		<fieldset><legend><?php echo $this->translate('labels.upload_media'); ?></legend>
			<input name="issue_id"   type="hidden" value="<?php echo $this->issue->getId(); ?>" />
			<input name="attachment" type="file" />
			<button type="submit">
				<span class="icon-upload"></span>
				<?php echo $this->translate('labels.upload'); ?>
			</button>
			<a class="btn" href="<?php echo $this->issue->getTicket()->getURL(); ?>">
				<span class="icon-remove"></span>
				<?php echo $this->translate('labels.cancel'); ?>
			</a>
		</fieldset>
	</form>
</div>
