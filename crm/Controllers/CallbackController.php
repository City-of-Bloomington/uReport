<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Controllers;
use Blossom\Classes\Controller;

class CallbackController extends Controller
{
	public function index()
	{
		$this->template->setFilename('callback');
	}
}
