<?php
/**
 * @copyright 2011-2021 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param \Solarium\QueryType\Select\Result\Result $this->result
 */
use Application\Models\BookmarkTable;
use Application\Models\Person;
use Application\Models\Search;
use Application\Models\TicketTable;

use Application\ActiveRecord;
use Blossom\Classes\Block;
use Blossom\Classes\Url;

?>
<section id="ticketSearchForm">
    <header>
        <h1><?= $this->_('search'); ?></h1>
        <div class="tools">
        <?php
            if (Person::isAllowed('tickets','add')) {
                $links = [
                    ['label'=>$this->_('tickets_open'), 'url'=>BASE_URI.'/tickets?status=open'],
                    ['label'=>$this->_('tickets_my'),   'url'=>BASE_URI."/tickets?status=open;assignedPerson_id={$_SESSION['USER']->getId()}"]
                ];

                if ($_SESSION['USER']->getDepartment_id()) {
                    $d = $_SESSION['USER']->getDepartment();
                    $links[] = ['label'=>$this->_('tickets_my_department'), 'url'=>BASE_URI."/tickets?status=open;department_id={$d->getId()}"];
                }

                $table = new BookmarkTable();
                $list  = $table->find(['person_id'=>$_SESSION['USER']->getId(), 'type'=>'search']);
                $c     = count($list);
                if ($c) {
                    $bookmarks = [];
                    foreach ($list as $b) {
                        $bookmarks[] = ['label'=>self::escape($b->getName()), 'url'=>$b->getRequestUri()];
                    }
                    $links[] = ['subgroup'=>$bookmarks];
                }
                $helper = $this->template->getHelper('dropdown');
                echo $helper->dropdown($links, $this->_(['bookmark', 'bookmarks', count($links)]), 'search_saved');
            }
        ?>
        </div>
    </header>
	<form method="get" action="<?= BASE_URI; ?>/tickets">
		<fieldset>
            <input name="query" value="<?= !empty($_GET['query']) ? self::escape($_GET['query']) : ''; ?>" />
		</fieldset>
		<fieldset id="advanced-search">
            <?php
                $currentUrl = new Url(Url::current_url(BASE_HOST));
                if ($currentUrl->page) { unset($currentUrl->page); }
                $html       = '';

                /**
                 * While we're iterating over all possible facets, we'll look
                 * inside the $currentFilters to see if each filter is active.
                 * If a filter is active, we will echo the $deleteUrl,
                 * insted of echo'ing a link to add the filter to the current search.
                 */
                $person       = isset($_SESSION['USER']) ? $_SESSION['USER'] : 'anonymous';
                $resultFacets = $this->result->getFacetSet()->getFacets();
                $helper       = $this->template->getHelper('buttonLink');

                foreach (Search::$facetFields as $f) {
                    if ($f['type'] == 'field') {
                        $key   = $f['field'];
                        $list  = [];
                        $class = '';
                        foreach ($resultFacets[$key] as $value=>$count) {
                            if ($count) {
                                $url = clone $currentUrl;
                                $url->$key = $value;
                                try {
                                    $displayValue = Search::getDisplayName($key, $value);

                                    // If the key has a current value, then start expanded
                                    if (!empty($_GET[$key])) {
                                        unset($url->$key);
                                        $label = $helper->buttonLink($url, $displayValue, 'delete');
                                        $class = 'open="true"';
                                    }
                                    else {
                                        $label = "<a href=\"$url\">$displayValue</a>";
                                    }

                                    $list[] = "<li>$label ($count)</li>";
                                }
                                catch (\Exception $e) {
                                    // Don't bother displaying any facets that are no longer in the system
                                }
                            }
                        }
                        if ($list) {
                            $label = $this->_($key);
                            $list  = implode('', $list);
                            $html.= "
                            <details $class>
                                <summary>$label</summary>
                                <ul>$list</ul>
                            </details>
                            ";
                        }
                    }
                }
                /**
                 * SLA is a facetQuery not a field
                 */

                $value = (int)$resultFacets['overdue']->getValue();
                if ($value) {
                    $url = clone($currentUrl);
                    $url->sla = 'overdue';

                    if (!empty($_GET['sla'])) {
                        unset($url->sla);
                        $label = $helper->buttonLink($url, $this->_('overdue'), 'delete');
                        $class = 'open="true"';
                    }
                    else {
                        $label = "<a href=\"$url\">{$this->_('overdue')}</a>";
                        $class = '';
                    }
                    $html.= "
                    <details $class>
                        <summary>{$this->_('sla')}</summary>
                        <ul><li>$label ($value)</li></ul>
                    </details>
                    ";
                }

                /**
                 * Date ranges
                 */
                $class = empty($_GET['enteredDate']) ? '' : 'open="true"';
                $html .= "
                <details $class>
                    <summary>{$this->_('enteredDate')}</summary>
                ";
                $fieldHelper = $this->template->getHelper('field');
                foreach (['start', 'end'] as $d) {
                    $value = '';
                    if (!empty($_GET['enteredDate'][$d])) {
                        $value = self::escape($_GET['enteredDate'][$d]);
                    }
                    $html.= $fieldHelper->field([
                        'name'  => "enteredDate[$d]",
                        'id'    => "enteredDate-$d",
                        'label' => $this->_($d),
                        'type'  => 'date',
                        'value' => $value
                    ]);
                }
                $html.= "
                </details>
                ";


				/**
				 * Any additional filters that are not being displayed as facets
				 */
// 				foreach ($currentFilters as $key=>$f) {
//                     $label = $key === 'bbox' ? $this->_($key) : $this->_($key).':'.$f['value'];
//                     echo $helper->buttonLink($f['deleteUrl'], $label, 'delete');
// 				}
				echo $html;
			?>
		</fieldset>


		<details>
            <summary><?= $this->_('display_fields'); ?></summary>
            <fieldset id="display_fields">
			<?php
				// Default columns to display
				if (!isset($_GET['fields'])) {
					$_GET['fields'] = TicketTable::$defaultFieldsToDisplay;
				}

				$values  = [];
				$options = [];
				foreach (TicketTable::getDisplayableFields() as $field=>$name) {
					if ($field != 'id') {
                        $options[] = ['value'=>$field, 'label'=>$this->_($field)];
                        if (isset($_GET['fields'][$field])) { $values[] = $field; }
					}
				}
				echo $fieldHelper->field([
                    'name'    => 'fields',
                    'type'    => 'checkbox',
                    'value'   => $values,
                    'options' => $options
				]);
			?>
            </fieldset>
        </details>


		<fieldset>
			<?php
				$h = $this->template->getHelper('renderInputs');
				echo $h->renderInputs($_GET, null, ['query', 'fields']);
			?>
			<button type="submit" class="search"><?= $this->_('search'); ?></button>
		</fieldset>
	</form>
	<?php
        if (Person::isAllowed('bookmarks', 'update')) {
            $bookmarkForm = new Block('bookmarks/updateForm.inc', ['type'=>'search']);
            echo $bookmarkForm->render('html', $this->template);
        }
	?>
</section>
