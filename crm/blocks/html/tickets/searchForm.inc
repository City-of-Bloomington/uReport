<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param SolrObject $this->solrObject
 */
$this->template->addToAsset('scripts',YUI.'/yui/yui-min.js');
$this->template->addToAsset('scripts',BASE_URL.'/js/tickets/search.js');
?>
<div class="searchForm">
	<?php
		if (userIsAllowed('tickets','add')) {
			// Solr stores ticket_id as 'id'
			$id = isset($_GET['id']) ? (int)$_GET['id'] : '';
			echo "
			<div class=\"quickSearches\">
				<h2>Quick Links</h2>
				<form method=\"get\" action=\"".BASE_URL."/tickets\">
					<fieldset>
 						<label>Case # <input name=\"id\" value=\"$id\" size=\"6\" /></label>
						<button type=\"submit\" class=\"search\">Go</button>
					</fieldset>
				</form>
				<ul><li><a href=\"".BASE_URL."/tickets?status=open\">
							Open Cases
						</a>
					</li>
					<li><a href=\"".BASE_URL."/tickets?status=open;assignedPerson_id={$_SESSION['USER']->getId()}\">
							My Cases
						</a>
					</li>
				";
				if ($_SESSION['USER']->getDepartment_id()) {
					$d = $_SESSION['USER']->getDepartment();
					echo "
					<li><a href=\"".BASE_URL."/tickets?status=open;department_id={$d->getId()}\">
							My Department's Cases
						</a>
					</li>
					";
				}
			echo "
				</ul>
			</div>
			";
		}
	?>
	<div class="advancedSearch">
		<h2>Search</h2>
		<form method="get" action="<?php echo BASE_URI; ?>/tickets">
			<fieldset>
				<label>Search
					<input name="query" value="<?php if (!empty($_GET['query'])) echo View::escape($_GET['query']); ?>" />
				</label>
			</fieldset>
			<fieldset id="advanced-search">
				<?php
					if (isset($this->solrObject->facet_counts->facet_fields)) {
						$person = isset($_SESSION['USER']) ? $_SESSION['USER'] : 'anonymous';
						$facetFields = $this->solrObject->facet_counts->facet_fields;
						$currentURL = new URL($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);

						foreach (Search::$facetFields['ticket'] as $key) {
							$list = '';
							foreach ($facetFields[$key] as $value=>$count) {
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
								<div><h3>$displayName</h3>
									<ul>$list</ul>
								</div>
								";
							}
						}
					}
				?>
			</fieldset>


			<fieldset><legend>Fields to display</legend>
				<ul class="fields">
				<?php
					// Default columns to display
					if (!isset($_GET['fields'])) {
						$_GET['fields'] = TicketList::$defaultFieldsToDisplay;
					}

					foreach (TicketList::getDisplayableFields() as $field=>$name) {
						if ((false === strpos($field,'Person') || userIsAllowed('people', 'view'))
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


			<fieldset><legend>Submit</legend>
				<?php
					$h = $this->template->getHelper('renderInputs');
					echo $h->renderInputs($_GET, null, array('query', 'fields'));
				?>
				<button type="submit" class="search">Search</button>
			</fieldset>
		</form>
	</div>
</div>
