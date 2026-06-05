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
 * @copyright 2013-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Controllers;

use Application\Block;
use Application\Controller;
use Application\Url;

class SolrController extends Controller
{
    public function index()
    {
        http_response_code(404);
        header('HTTP/1.1 404 Not Found', true, 404);
        $this->template->blocks = [ new Block('404.inc') ];

        // if (!empty($_GET['wt']) && $_GET['wt'] == 'json') {
        //     header('Content-type: text/json; charset=utf-8');
        // }
        // else {
        //     header('Content-type: text/xml; charset=utf-8');
        // }
        //
        // global $SOLR;
        // $config = $SOLR['ureport'];
        //
        // $protocol = $config['port']==443 ? 'http://' : 'http://';
        // $url = $protocol.$config['host'];
        // if ($config['port'] != 80) { $url.= ':'.$config['port']; }
        // $url.= '/solr/'.$config['core'].'/select?'.$_SERVER['QUERY_STRING'];
        //
        // echo Url::get($url);
        // exit();
    }
}
