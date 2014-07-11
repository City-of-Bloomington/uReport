<?php
/**
 * @copyright 2011-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Apache_Solr_Response $this->solrObject
 */
use Application\Models\BookmarkTable;
use Application\Models\Person;
use Application\Models\Search;
use Application\Models\TicketTable;

use Blossom\Classes\Block;
use Blossom\Classes\View;
use Blossom\Classes\Url;

$this->template->addToAsset('scripts',YUI.'/yui/yui-min.js');
#$this->template->addToAsset('scripts',BASE_URI.'/js/tickets/search.js');
$this->template->addToAsset('scripts',BASE_URI.'/js/collapsible.js');
if (Person::isAllowed('tickets','add')) {
	// Solr stores ticket_id as 'id'
	$id = isset($_GET['id']) ? (int)$_GET['id'] : '';
	echo "
	<div class=\"quickSearches\">
		<h2>{$this->translate('labels.quick_links')}</h2>
		<form method=\"get\" action=\"".BASE_URI."/tickets\">
			<fieldset>
				<label>
					{$this->translate(array('labels.ticket', 'labels.tickets', 1))}
					# <input name=\"id\" value=\"$id\" size=\"6\" />
				</label>
				<button type=\"submit\"><span class=\"icon-search\"></span> Go</button>
			</fieldset>
		</form>
		<ul><li><a href=\"".BASE_URI."/tickets?status=open\">
					{$this->translate('labels.tickets_open')}
				</a>
			</li>
			<li><a href=\"".BASE_URI."/tickets?status=open;assignedPerson_id={$_SESSION['USER']->getId()}\">
					{$this->translate('labels.tickets_my')}
				</a>
			</li>
		";
		if ($_SESSION['USER']->getDepartment_id()) {
			$d = $_SESSION['USER']->getDepartment();
			echo "
			<li><a href=\"".BASE_URI."/tickets?status=open;department_id={$d->getId()}\">
					{$this->translate('labels.tickets_my_department')}
				</a>
			</li>
			";
		}
	echo "
		</ul>
	";
	$table = new BookmarkTable();
	$bookmarks = new Block(
		'bookmarks/list.inc',
		array(
			'bookmarks' => $table->find(array(
				'person_id'=>$_SESSION['USER']->getId(),
				'type'=>'search'
			))
		)
	);
	echo $bookmarks->render('html', $this->template);

	echo "
	</div>
	";
}
?>
<div>
	<h2><?php echo $this->translate('labels.search'); ?></h2>
	<form method="get" action="<?php echo BASE_URI; ?>/tickets">
		<fieldset>
			<label><?php echo $this->translate('labels.search'); ?>
				<input name="query" value="<?php if (!empty($_GET['query'])) echo View::escape($_GET['query']); ?>" />
			</label>
			<button type="submit"><span class="fa fa-search"></span> <?php echo $this->translate('labels.go'); ?></button>
			<p class="help"><?php echo $this->translate('messages.ticket_search_query_description'); ?></p>
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
									$displayValue = Search::getDisplayName('ticket', $key, $value);
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
							<div class=\"collapsible\">
								<h3 class=\"hd\">$displayName</h3>
								<ul class=\"bd\">$list</ul>
							</div>
							";
						}
					}
				}
			?>
			<div class="collapsible">
				<h3 class="hd"><?php echo $this->translate('labels.enteredDate'); ?></h3>
				<table class="bd">
					<tr><td><label for="enteredDate-start"><?php echo $this->translate('labels.start'); ?>:</label></td>
						<td><input name="enteredDate[start]" id="enteredDate-start" size="10" maxlength="10"
								value="<?php echo !empty($_GET['enteredDate']['start']) ? View::escape($_GET['enteredDate']['start']) : ''; ?>" />
						</td>
					</tr>
					<tr><td><label for="enteredDate-end"><?php echo $this->translate('labels.end'); ?>:</label></td>
						<td><input name="enteredDate[end]" id="enteredDate-end" size="10" maxlength="10"
								value="<?php echo !empty($_GET['enteredDate']['end']) ? View::escape($_GET['enteredDate']['end']) : ''; ?>" />
						</td>
					</tr>
				</table>
			</div>
		</fieldset>


		<fieldset><legend><?php echo $this->translate('labels.display_fields'); ?></legend>
			<ul class="fields twoColumnlist">
			<?php
				// Default columns to display
				if (!isset($_GET['fields'])) {
					$_GET['fields'] = TicketTable::$defaultFieldsToDisplay;
				}

				foreach (TicketTable::getDisplayableFields() as $field=>$name) {
					if ((false === strpos($field,'Person') || Person::isAllowed('people', 'view'))
						&& $field!='id') {
						$checked = isset($_GET['fields'][$field]) ? 'checked="checked"' : '';
						echo "
						<li><label>
								<input name=\"fields[$field]\" id=\"fields-$field\" type=\"checkbox\" $checked />
								$name
							</label>
						</li>
						";
					}
				}
			?>
			</ul>
		</fieldset>


		<fieldset><legend><?php echo $this->translate('labels.submit'); ?></legend>
			<?php
				$h = $this->template->getHelper('renderInputs');
				echo $h->renderInputs($_GET, null, array('query', 'fields'));
			?>
			<button type="submit"><span class="fa fa-search"></span> <?php echo $this->translate('labels.search'); ?></button>
		</fieldset>
	</form>
</div>
<div>
	<p><?php echo $this->translate('text.disclaimer'); ?></p>
</div>
