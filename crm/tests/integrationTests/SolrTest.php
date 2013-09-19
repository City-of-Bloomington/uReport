<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
require_once __DIR__.'/../../configuration.inc';

class SolrTest extends PHPUnit_Framework_TestCase
{
	public function testParamsReturnedInSolrResponse()
	{
		$search = new Search();
		$solrResponse = $search->query(array());
		$this->assertTrue(isset($solrResponse->responseHeader), 'Solr response is missing responseHeader');
		$this->assertTrue(isset($solrResponse->responseHeader->params), 'Solr response is missing params in responseHeader');
	}
}
