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
		if ($this->template->outputFormat=='html') {
			$findForm = new Block('locations/findLocationForm.inc');
			$this->template->title = $this->template->_('find_location');
			$this->template->blocks[] = $findForm;
		}

		if (isset($_GET['location'])) {
			$results = new Block('locations/findLocationResults.inc', ['results' => Location::search($_GET['location'])]);

			if (isset($_GET['return_url'])) {
				$results->return_url = $_GET['return_url'];
			}

            $this->template->blocks[] = $results;
		}
		else {
			$this->template->blocks[] = new Block('locations/mapChooser.inc');
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

        $this->template->title = $location;
        $this->template->blocks[] = new Block('locations/locationInfo.inc', [
            'location'       => $location,
            'disableButtons' => isset($_GET['disableButtons'])
        ]);
	}
}
