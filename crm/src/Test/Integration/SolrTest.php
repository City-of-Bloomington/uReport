<?php
/**
 * @copyright 2013-2021 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Test\Integration;

use PHPUnit\Framework\TestCase;
use Application\Models\Search;

class SolrTest extends TestCase
{
    public function testSolr()
    {
        $search = new Search();
        $result = $search->query([]);
        $this->assertGreaterThan(0, $result->getNumFound(), "No tickets found");

        $facets = array_keys($result->getFacetSet()->getFacets());
        foreach (Search::$facetFields as $f) {
            if ($f['type'] == 'field') {
                $this->assertTrue(in_array($f['field'], $facets), "$f[field] facet not returned in result");
            }
        }
    }
}
