<?php
/**
 * Proxy URL for read-only Solr queries
 *
 * Solr does not have any security built in.
 * Javascript needs to be able to make read-only queries
 * to Solr via the same host that hosts the CRM.
 * This proxy URL provides a solution for both these problems.
 * Request parameters are forwarded to the /solr/select? url
 *
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Controllers;

use Blossom\Classes\Controller;
use Blossom\Classes\Url;

class SolrController extends Controller
{
	public function index()
	{
		if (!empty($_GET['wt']) && $_GET['wt'] == 'json') {
			header('Content-type: text/json; charset=utf-8');
		}
		else {
			header('Content-type: text/xml; charset=utf-8');
		}

		$protocol = SOLR_SERVER_PORT==443 ? 'http://' : 'http://';
		$url = $protocol.SOLR_SERVER_HOSTNAME;
		if (SOLR_SERVER_PORT != 80) { $url.= ':'.SOLR_SERVER_PORT; }
		$url.= SOLR_SERVER_PATH.'/select?'.$_SERVER['QUERY_STRING'];

		echo Url::get($url);
		exit();
	}
}
