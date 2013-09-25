<?php
/**
 * @copyright 2012-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param CategoryGroup $this->categoryGroup
 */
$title = $this->categoryGroup->getId()
	? $this->translate('labels.edit').' '.$this->translate(array('labels.categoryGroup','labels.categoryGroups',1))
	: $this->translate('labels.add' ).' '.$this->translate(array('labels.categoryGroup','labels.categoryGroups',1));
$name = View::escape($this->categoryGroup->getName());
?>
<div class="updateCategoryGroupForm">
	<h2><?php echo $title; ?></h2>
	<form method="post" action="<?php echo BASE_URI; ?>/categoryGroups/update">
		<fieldset>
			<input name="categoryGroup_id" type="hidden" value="<?php echo $this->categoryGroup->getId(); ?>" />
			<table>
				<tr><td><label for="name"><?php echo $this->translate('labels.name'); ?></label></td>
					<td><input name="name" id="name" value="<?php echo $name; ?>" /></td>
				</tr>
			</table>
			<?php
				$helper = $this->template->getHelper('saveAndCancelButtons');
				echo $helper->saveAndCancelButtons(BASE_URI.'/categoryGroups');
			?>
		</fieldset>
	</form>
</div>
