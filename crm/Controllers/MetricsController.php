<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Controllers;

use Application\Models\Metrics;

use Blossom\Classes\Block;
use Blossom\Classes\Controller;

class MetricsController extends Controller
{
    public function index()
    {
    }

    public function onTimePercentage()
    {
        if (!empty($_GET['category_id']) && !empty($_GET['numDays'])) {

            $effectiveDate = !empty($_GET['effectiveDate'])
                ? new \DateTime($_GET['effectiveDate'])
                : new \DateTime();

            $data = Metrics::onTimePercentage($_GET['category_id'], $_GET['numDays'], $effectiveDate);
            $this->template->blocks[] = new Block('metrics/metric.inc', ['response' => $data]);
        }
    }
}