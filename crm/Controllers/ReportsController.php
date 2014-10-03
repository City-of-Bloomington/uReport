<?php
/**
 * @copyright 2012-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Controllers;

use Application\Models\Report;

use Blossom\Classes\Block;
use Blossom\Classes\Controller;
use Blossom\Classes\Template;

class ReportsController extends Controller
{
	public function __construct(Template $template)
	{
		parent::__construct($template);
        $this->template->setFilename('search');
        $this->template->blocks['left'][] = new Block('reports/list.inc');
        $this->template->blocks['left'][] = new Block('reports/searchForm.inc');
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

	public function volume()
	{
        $this->template->blocks['right'][] = new Block(
            'reports/volume.inc'
        );
	}

	public function currentOpenTickets()
	{
        $data = Report::currentOpenTickets();
        $this->template->blocks['right'][] = new Block(
            'reports/ticketCounts.inc',
            ['data'=>$data, 'title'=>'Tickets currently open']
        );
	}

	public function openedTickets()
	{
        $this->template->blocks['right'][] = new Block(
            'reports/ticketCounts.inc',
            ['data'=>Report::openedTickets(), 'title'=>'Tickets opened today']
        );
	}

	public function closedTickets()
	{
        $this->template->blocks['right'][] = new Block(
            'reports/ticketCounts.inc',
            ['data'=>Report::closedTickets(), 'title'=>'Tickets closed today']
        );
	}
}
