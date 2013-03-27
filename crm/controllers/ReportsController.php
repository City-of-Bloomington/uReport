<?php
/**
 * @copyright 2012-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class ReportsController extends Controller
{
	public function __construct(Template $template)
	{
		parent::__construct($template);
		$this->template->setFilename('search');
		$this->template->blocks['left'][]    = new Block('reports/list.inc');
		$this->template->blocks['left'][]    = new Block('reports/searchForm.inc');
	}

	public function index()
	{
		$this->template->blocks['right'][] = new Block(
			'reports/activity.inc'
		);
		// Turn off the searchForm
		unset($this->template->blocks['left'][1]);
	}

	public function assignments()
	{
		$data = Report::assignments($_GET);
		$this->template->blocks['right'][] = new Block(
			'reports/assignments.inc', array('data'=>$data)
		);
	}

	public function categories()
	{
		$data = Report::categories($_GET);
		$this->template->blocks['right'][] = new Block(
			'reports/categories.inc', array('data'=>$data)
		);
	}

	public function sla()
	{
		$this->template->blocks['right'][] = new Block(
			'reports/sla.inc'
		);
	}
}