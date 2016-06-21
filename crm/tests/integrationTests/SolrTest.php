<?php
/**
 * @copyright 2013-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Application\Models\Search;

$_SERVER['SITE_HOME'] = __DIR__;
require_once '../../bootstrap.inc';

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
