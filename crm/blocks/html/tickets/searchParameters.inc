<?php
/**
 * Displays the fields the user searched on
 *
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param SolrObject $this->solrObject
 */
$currentURL = new URL($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);

if (!empty($_GET['query'])
	|| isset($this->solrObject->responseHeader->params->fq)) {
	$q = !empty($_GET['query'])
		? 'Results for: '.View::escape($_GET['query'])
		: 'Current Search';
	$fq = isset($this->solrObject->responseHeader->params->fq)
		? $this->solrObject->responseHeader->params->fq
		: array();
	echo "
	<div class=\"searchParameters\">
		<h2>$q</h2>
	";
	if (count($fq)) {
		echo "
		<p>Your current search parameters.
			Click on one of the labels to delete that parameter from your search.
		</p>
		<span>
		";
		// There may only be one filterQuery
		if (!is_array($fq)) { $fq = array($fq); }

		foreach ($fq as $facet) {
			preg_match('/([^:]+):(.+)/', $facet, $matches);
			$key = $matches[1];
			$value = $matches[2];
			// Users should not change the permission level they're searching as
			if ($key != 'displayPermissionLevel') {
				$deleteURL = clone $currentURL;
				if (isset($deleteURL->$key)) {
					unset($deleteURL->$key);
				}

				$displayName = Search::$searchableFields[$key];
				$displayValue = Search::getDisplayName('ticket', $key, $value);
				echo "
				<a class=\"minusTag button\" href=\"$deleteURL\">
					$displayName: $displayValue
				</a>
				";
			}
		}
		echo "</span>";
	}
	echo "
	</div>
	";
}
