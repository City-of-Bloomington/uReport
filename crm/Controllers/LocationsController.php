<?php
/**
 * @copyright 2012-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Controllers;

use Application\Models\Location;
use Application\Models\Person;
use Application\Models\TicketTable;

use Blossom\Classes\Block;
use Blossom\Classes\Controller;
use Blossom\Classes\Template;

class LocationsController extends Controller
{
	/**
	 * Search Locations
	 */
	public function index()
	{
		$this->template->setFilename('locations');

		if ($this->template->outputFormat=='html') {
			$findForm = new Block('locations/findLocationForm.inc');
			$this->template->blocks['panel-one'][] = $findForm;
		}

		if (isset($_GET['location'])) {
			$results = new Block('locations/findLocationResults.inc', ['results' => Location::search($_GET['location'])]);

			if (isset($_GET['return_url'])) {
				$results->return_url = $_GET['return_url'];
			}

			if ($this->template->outputFormat === 'html') {
				$this->template->blocks['panel-two'][] = $results;
			}
			else {
				$this->template->blocks[] = $results;
			}
		}
		else {
			$this->template->blocks['panel-two'][] = new Block('locations/mapChooser.inc');
		}
	}

	/**
	 * View a single location
	 */
	public function view()
	{
		// Make sure we have the location in the system
		$location = trim($_GET['location']);
		if (!$location) {
			header('Location: '.BASE_URL.'/locations');
			exit();
		}
		$table      = new TicketTable();
		$ticketList = $table->find(['location'=>$location]);

		$this->template->setFilename('locations');

        $this->template->blocks['panel-one'][] = new Block("locations/locationInfo.inc", [
            'location'       => $location,
            'disableButtons' => isset($_GET['disableButtons'])
        ]);

		if (Person::isAllowed('tickets','merge') && !isset($_GET['disableLinks']) && count($ticketList)>1) {
			$this->template->blocks['panel-two'][] = new Block('tickets/ticketSelectForMergeForm.inc');
		}
	}
}
