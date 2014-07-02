<?php
/**
 * @copyright 2013-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Application\Models\Search;

$_SERVER['SITE_HOME'] = __DIR__;
require_once '../../configuration.inc';

class SolrTest extends PHPUnit_Framework_TestCase
{
	public function testParamsReturnedInSolrResponse()
	{
		$q = [];
		$search = new Search();
		$solrResponse = $search->query($q);
		$this->assertTrue(isset($solrResponse->responseHeader), 'Solr response is missing responseHeader');
		$this->assertTrue(isset($solrResponse->responseHeader->params), 'Solr response is missing params in responseHeader');
	}
}
