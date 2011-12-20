<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$this->template->addToAsset('scripts',YUI.'/yui/yui-min.js');
$this->template->addToAsset('scripts',BASE_URL.'/js/open311/accordian.js');
?>
<div id="groupChooser">
	<h1>All Reports</h1>
	<?php
		foreach (Category::getDistinct('group') as $group) {
			if ($group) {
				$_GET['group'] = $group;
				include APPLICATION_HOME.'/blocks/html/open311/chooseService.inc';
			}
		}
	?>
</div>