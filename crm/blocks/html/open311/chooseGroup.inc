<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
?>
<div class="chooseGroup">
	<h1>Groups</h1>
	<?php
		foreach (Category::getDistinct('group') as $group) {
			if ($group) {
				$_GET['group'] = $group;
				include APPLICATION_HOME.'/blocks/html/open311/chooseService.inc';
			}
		}
	?>
</div>