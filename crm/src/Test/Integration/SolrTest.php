<?php
/**
 * @copyright 2013-2019 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Test\Integration;

use PHPUnit\Framework\TestCase;
use Application\Models\Search;

class SolrTest extends TestCase
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
