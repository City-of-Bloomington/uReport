<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param PersonList $this->personList
 * @param bool $this->disableButtons
 */
use Application\Models\Person;
use Blossom\Classes\Url;
?>
<div class="findPeopleResults">
	<h2><?= $this->_('search_results'); ?></h2>
	<?php
		if (count($this->personList)) {
			$this->_include('people/personList.inc');
		}
		else {
			echo '<p>No results found</p>';
		}
		if (!$this->disableButtons && Person::isAllowed('people','add')) {
			$this->_include('people/personSelectForMerge.inc');


			// Preserve any extra parameters passed in
			$url = new Url(BASE_URL.'/people/update');
			foreach ($_REQUEST as $key=>$value) {
                $url->$key = $value;
			}
			$h = $this->template->getHelper('buttonLink');
			echo $h->buttonLink(
				$url,
				$this->_('person_add'),
				'add'
			);
		}
	?>
</div>
