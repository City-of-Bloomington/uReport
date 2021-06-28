<?php
/**
 * @copyright 2016-2021 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param \Solarium\QueryType\Select\Result\Result $this->result
 * @param bool                 $this->paginated   Whether the response is paginated or not
 */
use Application\Models\Person;
use Blossom\Classes\Url;

echo '<div id="resultFormatButtons">';
    $url    = new Url(URl::current_url(BASE_HOST));
    $helper = $this->template->getHelper('buttonLink');
    $format = $_GET['format'] ?? 'text';

    unset ($url->format);
    echo $helper->buttonLink($url,
                             $this->_('search_results_text'),
                             $format == 'text' ? 'text current' : 'text');

    $url->format = 'map';
    echo $helper->buttonLink($url,
                             $this->_('search_results_map'),
                             $format == 'map' ? 'map current' : 'map');

    // Users should filter the results somehow before they are allowed to
    // call for the full, raw results
    $options = $this->result->getQuery()->getOptions();
    if (count($options['filterquery']) > 0 && Person::isAllowed('tickets', 'print')) {
        $url->format = 'print';
        echo $helper->buttonLink($url,
                                 $this->_('search_results_print'),
                                 $format === 'print' ? 'print current' : 'print');
        $url->format = 'csv';
        echo $helper->buttonLink($url,
                                 $this->_('search_results_csv'),
                                 $format === 'csv' ? 'csv current' : 'csv');
    }
echo "</div>";
