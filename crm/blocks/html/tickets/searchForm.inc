<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Apache_Solr_Response $this->solrObject
 */
use Application\Models\BookmarkTable;
use Application\Models\Person;
use Application\Models\Search;
use Application\Models\TicketTable;

use Blossom\Classes\ActiveRecord;
use Blossom\Classes\Block;
use Blossom\Classes\Url;

$this->template->addToAsset('scripts', JQUERY.'/jquery.min.js');
$this->template->addToAsset('scripts', BASE_URI.'/js/collapsible.js');
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
                    $bookmarks = [['label'=>$this->_(['bookmark', 'bookmarks', $c]), 'url'=>BASE_URI.'/bookmarks']];
                    foreach ($list as $b) {
                        $bookmarks[] = ['label'=>self::escape($b->getName()), 'url'=>$b->getRequestUri()];
                    }
                    $links[] = ['subgroup'=>$bookmarks];
                }
                $helper = $this->template->getHelper('dropdown');
                echo $helper->dropdown($links, $this->_('quick_links'), 'quick_links');
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
                $currentUrl = new Url(Url::current_url());
                $html       = '';

                /**
                 * While we're iterating over all possible facets, we'll look
                 * inside the $currentFilters to see if each filter is active.
                 * If a filter is active, we will echo the $deleteUrl,
                 * insted of echo'ing a link to add the filter to the current search.
                 */
                $currentFilters = Search::getCurrentFilters($this->solrObject);

				if (isset($this->solrObject->facet_counts->facet_fields)) {
					$person      = isset($_SESSION['USER']) ? $_SESSION['USER'] : 'anonymous';
					$facetFields = $this->solrObject->facet_counts->facet_fields;
					$helper      = $this->template->getHelper('buttonLink');

					foreach (Search::$facetFields['ticket'] as $key) {
						$list  = [];
						$class = 'class="collapsible"';
						foreach ($facetFields->$key as $value=>$count) {
							if ($count) {
								$url = clone $currentUrl;
								$url->$key = $value;
								try {
									$displayValue = Search::getDisplayName($key, $value);

									if (array_key_exists($key, $currentFilters) && $currentFilters[$key]['value'] == $value) {
                                        $url   = $currentFilters[$key]['deleteUrl'];
                                        $label = $helper->buttonLink($url, $displayValue, 'delete');
                                        $class = '';
                                        unset($currentFilters[$key]);
									}
									else {
                                        $label = "<a href=\"$url\">$displayValue</a>";
									}

									$list[] = "<li>$label ($count)</li>";
								}
								catch (Exception $e) {
									// Don't bother displaying any facets that are no longer in the system
								}
							}
						}
						if ($list) {
							$label = $this->_($key);
							$list  = implode('', $list);
							$html.= "
							<nav $class>
								<h1>$label</h1>
								<ul>$list</ul>
							</nav>
							";
						}
					}
					/**
					 * SLA is a special type of facet
					 */
					$value = (int)$this->solrObject->facet_counts->facet_queries->{Search::SLA_OVERDUE_FUNCTION};
					if ($value) {
                        if (array_key_exists('sla', $currentFilters) && $currentFilters['sla']['value'] === 'overdue') {
                            $label = $helper->buttonLink($currentFilters['sla']['deleteUrl'], $this->_('overdue'), 'delete');
                            $class = '';
                        }
                        else {
                            $url      = clone $currentUrl;
                            $url->sla = 'overdue';
                            $label    = "<a href=\"$url\">{$this->_('overdue')}</a>";
                            $class    = 'class="collapsible"';
                        }
                        $html.= "
                        <nav $class>
                            <h1>{$this->_('sla')}</h1>
                            <ul><li>$label ($value)</li></ul>
                        </nav>
                        ";
                    }

                    /**
                     * Date ranges are a special type of facet
                     */
                    $class = 'class="collapsible"';
                    if (array_key_exists('enteredDate', $currentFilters)) {
                        $class = '';
                        unset($currentFilters['enteredDate']);
                    }
                    $html.= "
                    <nav $class>
                        <h1>{$this->_('enteredDate')}</h1>
                    ";
                    $fieldHelper = $this->template->getHelper('field');
                    foreach (['start', 'end'] as $d) {
                        $$d = '';
                        if (!empty($_GET['enteredDate'][$d])) {
                            $date = ActiveRecord::parseDate($_GET['enteredDate'][$d], DATE_FORMAT);
                            $$d = $date->format('U');
                        }
                        $html.= $fieldHelper->field([
                            'name'  => "enteredDate[$d]",
                            'id'    => "enteredDate-$d",
                            'label' => $this->_($d),
                            'type'  => 'date',
                            'value' => $$d
                        ]);
                    }
                    $html.= "
                    </nav>
                    ";
				}

				/**
				 * Any additional filters that are not being displayed as facets
				 */
				foreach ($currentFilters as $key=>$f) {
                    $label = $key === 'bbox' ? $this->_($key) : $this->_($key).':'.$f['value'];
                    echo $helper->buttonLink($f['deleteUrl'], $label, 'delete');
				}
				echo $html;
			?>
		</fieldset>


		<fieldset id="display_fields" class="collapsible">
            <legend><?= $this->_('display_fields'); ?></legend>
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


		<fieldset><legend><?= $this->_('submit'); ?></legend>
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
