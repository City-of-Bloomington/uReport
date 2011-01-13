<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param ConstituentList $this->constituentList
 */
echo '<h1>Search Results</h1>';

if (count($this->constituentList)) {
	include APPLICATION_HOME.'/blocks/html/constituents/constituentList.inc';
}
else {
	echo '<p>No results found</p>';
}

echo "<p>Don't see what you're looking for?</p>";
$block = new Block('constituents/updateConstituentForm.inc',
					array('constituent'=>new Constituent(),
							'return_url'=>$_SERVER['REQUEST_URI']));
echo $block->render('html',$this->template);