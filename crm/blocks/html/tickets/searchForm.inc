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

use Blossom\Classes\Block;
use Blossom\Classes\Url;

$this->template->addToAsset('scripts', JQUERY.'/jquery.min.js');
$this->template->addToAsset('scripts', BASE_URI.'/js/collapsible.js');
if (Person::isAllowed('tickets','add')) {
	// Solr stores ticket_id as 'id'
	$id = isset($_GET['id']) ? (int)$_GET['id'] : '';
	echo "
	<section class=\"quickSearches\">
        <header>
            <h1>{$this->_('quick_links')}</h1>
        </header>
		<form method=\"get\" action=\"".BASE_URI."/tickets\">
			<fieldset>
				<label>
					{$this->_('ticket')}
					# <input name=\"id\" value=\"$id\" size=\"6\" />
				</label>
				<button type=\"submit\" class=\"search\">{$this->_('go')}</button>
			</fieldset>
		</form>
		<ul><li><a href=\"".BASE_URI."/tickets?status=open\">
					{$this->_('tickets_open')}
				</a>
			</li>
			<li><a href=\"".BASE_URI."/tickets?status=open;assignedPerson_id={$_SESSION['USER']->getId()}\">
					{$this->_('tickets_my')}
				</a>
			</li>
		";
		if ($_SESSION['USER']->getDepartment_id()) {
			$d = $_SESSION['USER']->getDepartment();
			echo "
			<li><a href=\"".BASE_URI."/tickets?status=open;department_id={$d->getId()}\">
					{$this->_('tickets_my_department')}
				</a>
			</li>
			";
		}
	echo "
		</ul>
	";
	$table = new BookmarkTable();
	$bookmarks = new Block(
		'bookmarks/list.inc', [
			'bookmarks' => $table->find([
				'person_id' => $_SESSION['USER']->getId(),
				'type'      => 'search'
			])
		]
	);
	echo $bookmarks->render('html', $this->template);

	echo "
	</section>
	";
}
?>
<section id="ticketSearchForm">
    <header>
        <h1><?= $this->_('search'); ?></h1>
    </header>
	<form method="get" action="<?= BASE_URI; ?>/tickets">
		<fieldset>
			<label><?= $this->_('search'); ?>
				<input name="query" value="<?php if (!empty($_GET['query'])) echo self::escape($_GET['query']); ?>" />
			</label>
			<button type="submit" class="search"><?= $this->_('go'); ?></button>
			<p class="help"><?= $this->_('ticket_search_query_description', 'messages'); ?></p>
		</fieldset>
		<fieldset id="advanced-search">
			<?php
				if (isset($this->solrObject->facet_counts->facet_fields)) {
					$person = isset($_SESSION['USER']) ? $_SESSION['USER'] : 'anonymous';
					$facetFields = $this->solrObject->facet_counts->facet_fields;
					$currentURL = new Url($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);

					foreach (Search::$facetFields['ticket'] as $key) {
						$list = '';
						foreach ($facetFields->$key as $value=>$count) {
							if ($count) {
								$url = clone $currentURL;
								$url->$key = $value;
								try {
									$displayValue = Search::getDisplayName($key, $value);
									$list.= "<li><a href=\"$url\">$displayValue</a> ($count)</li>";
								}
								catch (Exception $e) {
									// Don't bother displaying any facets that are no longer in the system
								}
							}
						}
						if ($list) {
							$displayName = Search::$searchableFields[$key];
							echo "
							<nav class=\"collapsible\">
								<h1>$displayName</h1>
								<ul>$list</ul>
							</nav>
							";
						}
					}
					$value = $this->solrObject->facet_counts->facet_queries->{Search::SLA_OVERDUE_FUNCTION};
					$url = clone $currentURL;
					$url->sla = 'overdue';
					echo "
                    <nav class=\"collapsible\">
                        <h1>{$this->_('sla')}</h1>
                        <ul><li><a href=\"$url\">{$this->_('overdue')}</a> ($value)</li></ul>
                    </nav>
                    ";
				}
			?>
			<nav class="collapsible">
				<h1><?= $this->_('enteredDate'); ?></h1>
				<?php
                    $helper = $this->template->getHelper('field');
                    echo $helper->field([
                        'label' => $this->_('start'),
                        'name'  => 'enteredDate[start]',
                        'id'    => 'enteredDate-start',
                        'type'  => 'date',
                        'value' => !empty($_GET['enteredDate']['start']) ? self::escape($_GET['enteredDate']['start']) : ''
                    ]);
                    echo $helper->field([
                        'label' => $this->_('end'),
                        'name'  => 'enteredDate[end]',
                        'id'    => 'enteredDate-end',
                        'type'  => 'date',
                        'value' => !empty($_GET['enteredDate']['end']) ? self::escape($_GET['enteredDate']['end']) : ''
                    ]);
				?>
			</nav>
		</fieldset>


		<fieldset id="display_fields">
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
				echo $helper->field([
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
				echo $h->renderInputs($_GET, null, array('query', 'fields'));
			?>
			<button type="submit" class="search"><?= $this->_('search'); ?></button>
		</fieldset>
	</form>
</section>
<div>
	<p><?= $this->_('disclaimer', 'messages'); ?></p>
</div>
