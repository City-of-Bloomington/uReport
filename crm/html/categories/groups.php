<?php
/**
 * Displays the list of distinct category groups
 *
 * This script is primarily intended for web service calls.
 * Although viewing it as HTML won't hurt anything
 *
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$template = !empty($_GET['format']) ? new Template('default',$_GET['format']) : new Template('two-column');
$template->blocks[] = new Block(
	'categories/groups.inc',
	array('groups'=>Category::getDistinct('group'))
);
echo $template->render();