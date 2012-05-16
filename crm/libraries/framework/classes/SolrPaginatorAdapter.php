<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class SolrPaginatorAdapter implements Zend_Paginator_Adapter_Interface
{
	private $solrObject;

	public function __construct(SolrObject $solrObject)
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