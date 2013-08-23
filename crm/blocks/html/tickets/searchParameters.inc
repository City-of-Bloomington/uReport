<?php
/**
 * Displays the fields the user searched on
 *
 * @copyright 2011-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Apache_Solr_Response $this->solrObject
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

	if (userIsAllowed('bookmarks', 'update')) {
		$bookmarkForm = new Block('bookmarks/updateForm.inc',array('type'=>'search'));
		echo $bookmarkForm->render('html', $this->template);
	}

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
				
				if($key === 'coordinates') {
					$key = 'bbox';
					if (isset($deleteURL->$key)) {
						unset($deleteURL->$key);
					}
					$displayName = Search::$searchableFields[$key];
					echo "
					<a class=\"btn\" href=\"$deleteURL\">
						<span class=\"icon-remove\"></span>
						$displayName
					</a>
					";
				}
				else {
					if (isset($deleteURL->$key)) {
						unset($deleteURL->$key);
					}
					$displayName = Search::$searchableFields[$key];
					$displayValue = Search::getDisplayName('ticket', $key, $value);
					echo "
					<a class=\"btn\" href=\"$deleteURL\">
						<span class=\"icon-remove\"></span>
						$displayName: $displayValue
					</a>
					";
				}
			}
		}
		echo "</span>";
	}
	echo "
	</div>
	";
	//start editing by Quan
	if(!isset($_GET['resultFormat'])) {
		if($currentURL->parameters) {
			echo "
			<a id='text-result' class='btn' href=\"".$currentURL.";resultFormat=text\"> <span class=\"icon-list-ul\"></span> Text Results </a>
			<a id='map-result' class='btn' href=\"".$currentURL.";resultFormat=map\"> <span class=\"icon-map-marker\"></span> Map Results </a>
			";
		}
		else {
			echo "
			<a id='text-result' class='btn' href=\"".$currentURL."?resultFormat=text\"> <span class=\"icon-list-ul\"></span> Text Results </a>
			<a id='map-result' class='btn' href=\"".$currentURL."?resultFormat=map\"> <span class=\"icon-map-marker\"></span> Map Results </a>
			";
		}
	}
	else {
		$tempURL = clone $currentURL;
		$tempURL->resultFormat = 'text';
		echo "<a id='text-result' class='btn' href=\"".$tempURL."\"> <span class=\"icon-list-ul\"></span> Text Results </a>";
		$tempURL->resultFormat = 'map';
		echo "<a id='map-result' class='btn' href=\"".$tempURL."\"> <span class=\"icon-map-marker\"></span> Map Results </a>";
	}
	//end editing by Quan
}
