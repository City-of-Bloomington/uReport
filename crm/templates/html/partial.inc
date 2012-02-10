<?php
/**
 * This template renders a single panel or block
 *
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
include FRAMEWORK.'/errorMessages.php';
echo $this->includeBlocks($_REQUEST['partial']);
