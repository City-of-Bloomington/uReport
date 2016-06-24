<?php
/**
 * @copyright 2012-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param IssueType $this->issueType
 */
use Blossom\Classes\View;

$name = self::escape($this->issueType->getName());
$title = $this->issueType->getId()
	? $this->_('edit').' '.$this->_(array('issueType', 'issueTypes', 1))
	: $this->_('add' ).' '.$this->_(array('issueType', 'issueTypes', 1));
?>
<div>
	<h2><?= $title; ?></h2>
	<form method="post" action="<?= BASE_URI; ?>/issueTypes/update">
		<input type="hidden" name="issueType_id" value="<?= $this->issueType->getId(); ?>" />

		<fieldset>
			<table>
				<tr><td><label for="name"><?= $this->_('name'); ?></label></td>
					<td><input name="name" id="name" value="<?= $name; ?>" /></td>
				</tr>
			</table>
			<?php
				$helper = $this->template->getHelper('saveAndCancelButtons');
				echo $helper->saveAndCancelButtons(BASE_URI.'/issueTypes');
			?>
		</fieldset>
	</form>
</div>
