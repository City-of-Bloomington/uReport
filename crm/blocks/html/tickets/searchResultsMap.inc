<?php
/**
 * Displays a map of the search results
 *
 * @copyright 2013-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Apache_Solr_Response $this->solrObject
 */
use Blossom\Classes\Block;
?>
<div class="searchResults">
<?php
	$c = $this->solrObject->response->numFound;
	if ($c) {
		echo "<h2>{$this->_('search_results')}</h2>";
	}
	$map = new Block('tickets/partials/searchResultsMap.inc', ['solrObject'=>$this->solrObject]);
	echo $map->render('html', $this->template);
?>
</div>
