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
		<p>{$this->translate('text.search_parameters_description')}</p>
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
					if (isset($deleteURL->zoom)) { unset($deleteURL->zoom); }
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
					if ($displayValue) {
						echo "
						<a class=\"btn\" href=\"$deleteURL\">
							<span class=\"icon-remove\"></span>
							$displayName: $displayValue
						</a>
						";
					}
				}
			}
		}
		echo "</span>";
	}
	echo "
	</div>
	";
}
echo '<div id="resultFormatButtons">';
	$url = clone $currentURL;

	$resultFormat = (isset($_GET['resultFormat'])) ? trim($_GET['resultFormat']) : 'text';

	unset($url->resultFormat);
	$current = $resultFormat=='text' ? ' current' : '';
	echo "
	<a class=\"btn$current\" href=\"$url\">
		<span class=\"icon-list-ul\"></span>
		{$this->translate('labels.search_results_text')}
	</a>
	";

	$url->resultFormat = 'map';
	$current = $resultFormat=='map' ? ' current' : '';
	echo "
	<a class=\"btn$current\" href=\"$url\">
		<span class=\"icon-map-marker\"></span>
		{$this->translate('labels.search_results_map')}
	</a>
	";

	// Users should filter the results somehow before they are allowed to
	// call for the full, raw results
	if (isset($fq) && count($fq) && userIsAllowed('tickets', 'print')) {
		$url->resultFormat = 'raw';
		$current = $resultFormat=='raw' ? ' current' : '';
		echo "
		<a class=\"$current\" href=\"$url\">
			<span class=\"icon-print\"></span>
			{$this->translate('labels.search_results_print')}
		</a>
		";
	}
echo "</div>";
