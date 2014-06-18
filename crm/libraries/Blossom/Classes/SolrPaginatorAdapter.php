<?php
/**
 * @copyright 2012-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Blossom\Classes;

class SolrPaginatorAdapter implements \Zend\Paginator\Adapter\AdapterInterface
{
	private $solrObject;

	public function __construct(\Apache_Solr_Response $solrObject)
	{
		$this->solrObject = $solrObject;
	}

	public function count()
	{
		return $this->solrObject->response->numFound;
	}

	public function getItems($offset, $itemCountPerPage)
	{
		return array();
	}
}
