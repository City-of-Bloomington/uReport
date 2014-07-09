<?php
/**
 * @copyright 2012-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Label $this->contactMethod
 */
use Blossom\Classes\View;

$name = View::escape($this->contactMethod->getName());
$title = $this->contactMethod->getId()
	? $this->translate('labels.edit').' '.$this->translate(array('labels.contactMethod','labels.contactMethods',1))
	: $this->translate('labels.add' ).' '.$this->translate(array('labels.contactMethod','labels.contactMethods',1));
?>
<div>
	<h2><?php echo $title; ?></h2>
	<form method="post" action="<?php echo BASE_URI; ?>/contactMethods/update">
		<input type="hidden" name="contactMethod_id" value="<?php echo $this->contactMethod->getId(); ?>" />

		<fieldset>
			<table>
				<tr><td><label for="name"><?php echo $this->translate('labels.name'); ?></label></td>
					<td><input name="name" id="name" value="<?php echo $name; ?>" /></td>
				</tr>
			</table>
			<?php
				$helper = $this->template->getHelper('saveAndCancelButtons');
				echo $helper->saveAndCancelButtons(BASE_URI.'/contactMethods');
			?>
		</fieldset>
	</form>
</div>
