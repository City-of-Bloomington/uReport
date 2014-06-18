<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class IndexController extends Controller
{
	public function index()
	{
		header('Location: '.BASE_URL.'/tickets');
		exit();
	}
}