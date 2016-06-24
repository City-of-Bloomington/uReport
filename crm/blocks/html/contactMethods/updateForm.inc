<?php
/**
 * @copyright 2012-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Label $this->contactMethod
 */
use Blossom\Classes\View;

$name = self::escape($this->contactMethod->getName());
$title = $this->contactMethod->getId()
	? $this->_('edit').' '.$this->_(array('contactMethod','contactMethods',1))
	: $this->_('add' ).' '.$this->_(array('contactMethod','contactMethods',1));
?>
<div>
	<h2><?= $title; ?></h2>
	<form method="post" action="<?= BASE_URI; ?>/contactMethods/update">
		<input type="hidden" name="contactMethod_id" value="<?= $this->contactMethod->getId(); ?>" />

		<fieldset>
			<table>
				<tr><td><label for="name"><?= $this->_('name'); ?></label></td>
					<td><input name="name" id="name" value="<?= $name; ?>" /></td>
				</tr>
			</table>
			<?php
				$helper = $this->template->getHelper('saveAndCancelButtons');
				echo $helper->saveAndCancelButtons(BASE_URI.'/contactMethods');
			?>
		</fieldset>
	</form>
</div>
