<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Label $this->label
 */
use Blossom\Classes\View;

$name = View::escape($this->label->getName());
$title = $this->label->getId()
	? $this->translate('labels.edit').' '.$this->translate(array('labels.label', 'labels.label', 1))
	: $this->translate('labels.add' ).' '.$this->translate(array('labels.label', 'labels.label', 1));
?>
<div>
	<h2><?php echo $title; ?></h2>
	<form method="post" action="<?php echo BASE_URI; ?>/labels/update">
		<input type="hidden" name="label_id" value="<?php echo $this->label->getId(); ?>" />

		<fieldset>
			<table>
				<tr><td><label for="name"><?php echo $this->translate('labels.name'); ?></label></td>
					<td><input name="name" id="name" value="<?php echo $name; ?>" /></td>
				</tr>
			</table>
			<?php
				$helper = $this->template->getHelper('saveAndCancelButtons');
				echo $helper->saveAndCancelButtons(BASE_URI.'/labels');
			?>
		</fieldset>
	</form>
</div>
