<?php
/**
 * @copyright 2012-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param CategoryGroup $this->categoryGroup
 */
use Blossom\Classes\View;

$title = $this->categoryGroup->getId()
	? $this->_('edit').' '.$this->_(array('categoryGroup','categoryGroups',1))
	: $this->_('add' ).' '.$this->_(array('categoryGroup','categoryGroups',1));
$name = self::escape($this->categoryGroup->getName());
?>
<div class="updateCategoryGroupForm">
	<h2><?= $title; ?></h2>
	<form method="post" action="<?= BASE_URI; ?>/categoryGroups/update">
		<fieldset>
			<input name="categoryGroup_id" type="hidden" value="<?= $this->categoryGroup->getId(); ?>" />
			<table>
				<tr><td><label for="name"><?= $this->_('name'); ?></label></td>
					<td><input name="name" id="name" value="<?= $name; ?>" /></td>
				</tr>
			</table>
			<?php
				$helper = $this->template->getHelper('saveAndCancelButtons');
				echo $helper->saveAndCancelButtons(BASE_URI.'/categoryGroups');
			?>
		</fieldset>
	</form>
</div>
