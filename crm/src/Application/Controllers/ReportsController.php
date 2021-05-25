<?php
/**
 * @copyright 2012-2021 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
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
        if ($this->template->outputFormat == 'html') {
            $this->template->setFilename('search');
            $this->template->blocks['panel-one'][] = new Block('reports/list.inc');
            $this->template->blocks['panel-one'][] = new Block('reports/searchForm.inc');
        }
	}

	public function index()
	{
        $this->template->title = $this->template->_('activity');
		$this->template->blocks[] = new Block('reports/activity.inc');
		// Turn off the searchForm
		unset($this->template->blocks['panel-one'][1]);
	}

	public function assignments()
	{
		$data = Report::assignments($_GET);
		$this->template->title = $this->template->_(['assignment', 'assignments', count($data)]);
		$this->template->blocks[] = new Block('reports/assignments.inc', ['data'=>$data]);
	}

	public function categories()
	{
		$data = Report::categories($_GET);
		$this->template->title = $this->template->_(['category', 'categories', count($data)]);
		$this->template->blocks[] = new Block('reports/categories.inc', ['data'=>$data]);
	}

	public function data()
	{
        $data = ($this->template->outputFormat != 'html' && !empty($_GET['categories']))
              ? Report::data($_GET)
              : [];
        $this->template->title    = $this->template->_('data');
        $this->template->blocks[] = new Block('reports/data.inc', ['data'=>$data]);
	}

	public function staff()
	{
        $data = Report::staff($_GET);
        $this->template->title = $this->template->_('staff');
        $this->template->blocks[] = new Block('reports/staff.inc', ['data'=>$data]);
	}

	public function person()
	{
        $data = Report::person($_GET);
        $this->template->title = $this->template->_('staff');
        $this->template->blocks[] = new Block('reports/person.inc', ['data'=>$data]);
	}

	public function sla()
	{
        $this->template->title = $this->template->_('sla');
		$this->template->blocks[] = new Block('reports/sla.inc');
	}

	public function volume()
	{
        $this->template->title = $this->template->_('volume');
        $this->template->blocks[] = new Block('reports/volume.inc');
	}

	public function currentOpenTickets()
	{
        $data = Report::currentOpenTickets();
        $this->template->title = $this->template->_('open_current');
        $this->template->blocks[] = new Block(
            'reports/ticketCounts.inc',
            ['data'=>$data, 'title'=>$this->template->_('open_current')]
        );
	}

	public function openedTickets()
	{
        $this->template->title = $this->template->_('open_today');
        $this->template->blocks[] = new Block(
            'reports/ticketCounts.inc',
            ['data'=>Report::openedTickets(), 'title'=>$this->template->_('open_today')]
        );
	}

	public function closedTickets()
	{
        $this->template->title = $this->template->_('closed_today');
        $this->template->blocks[] = new Block(
            'reports/ticketCounts.inc',
            ['data'=>Report::closedTickets(), 'title'=>$this->template->_('closed_today')]
        );
	}
}
