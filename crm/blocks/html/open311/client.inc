<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
?>
<div id="open311_client">
	<?php
		if (isset($_GET['service_code'])) {
			include APPLICATION_HOME.'/blocks/html/open311/clientRequestForm.inc';
		}
		elseif (isset($_GET['group'])) {
			include APPLICATION_HOME.'/blocks/html/open311/chooseService.inc';
		}
		else {
			include APPLICATION_HOME.'/blocks/html/open311/chooseGroup.inc';
		}
	?>
</div>