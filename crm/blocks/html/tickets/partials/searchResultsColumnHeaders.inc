<?php
/**
 * @copyright 2011-2021 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param \Solarium\QueryType\Select\Result\Result $this->result
 * @param array                $this->fields      Ticket fields the user asked to see
 */
use Application\Models\Person;
use Application\Models\Search;
use Application\Models\TicketTable;
use Blossom\Classes\Url;

$c = $this->result->getNumFound();
?>
<header>
    <h1><?= "{$this->_('search_results')}: ($c)"; ?></h1>
    <div class="tools">
        <?php
            $sortingLinks = [];

            if ($c) {
                $currentSort = $_GET['sort'] ?? Search::$defaultSort;

                $url               = new Url(Url::current_url(BASE_HOST));
                $displayableFields = TicketTable::getDisplayableFields();
                $sortableFields    = Search::$sortableFields;

                // Loop through all the fields the user asked to see
                // The key will be the fieldname and the value will be "On" or 1
                // or whatever the browser gives to checkboxes when you post a form.
                // We'll loop through all of the fieldnames that were checked
                foreach ($this->fields as $name => $checked) {
                    // Make sure they're allowed to see this field
                    if (array_key_exists($name, $displayableFields)) {

                        $label = $displayableFields[$name];

                        // _id fields are sorted using fields without _id in the fieldname
                        if (substr($name, -3) == '_id') { $name = substr($name, 0, -3); }
                        // If this field is being used for sorting,
                        // draw a url that reverses the sort order
                        if (in_array($name, $sortableFields)) {
                            if (!empty($currentSort[$name])) {
                                if ($currentSort[$name] == 'asc') {
                                    $url->sort = [$name   => 'desc'];
                                    $class     = ['class' => 'asc' ];
                                }
                                else {
                                    $url->sort = [$name   => 'asc' ];
                                    $class     = ['class' => 'desc'];
                                }
                            }
                            else {
                                $url->sort = [$name => 'asc'];
                                $class     = [];
                            }
                            $icon = '';
                            $sortingLinks[] = ['url'=>$url->__toString(), 'label'=>$label, 'attrs'=>$class];
                        }
                    }
                }
            }

            if (count($sortingLinks)) {
                $helper = $this->template->getHelper('dropdown');
                echo $helper->dropdown($sortingLinks, $this->_('sort_by'), 'sortingLinks');
            }
        ?>
    </div>
</header>
