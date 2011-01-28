<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param PersonList $this->personList
 */
echo '<h1>Search Results</h1>';

if (count($this->personList)) {
	include APPLICATION_HOME.'/blocks/html/people/personList.inc';
}
else {
	echo '<p>No results found</p>';
}
