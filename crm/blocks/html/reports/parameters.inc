<?php
/**
 * Displays the fields the user searched on
 *
 * @copyright 2012-2019 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
use Application\Models\AddressService;
use Application\Models\Category;
use Application\Models\Client;
use Application\Models\Department;
use Application\Models\Person;

use Blossom\Classes\Block;
use Blossom\Classes\Url;

$currentURL = new Url($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
?>
<div class="searchParameters">
	<?php
		echo '<div>';
		if (   !empty($_GET['enteredDate'])
			|| !empty($_GET['departments'])
			|| !empty($_GET['categories'])
			|| !empty($_GET['postingPermissionLevel'])
			|| !empty($_GET['clients'])) {
			if (Person::isAllowed('bookmarks', 'update')) {
				$bookmarkForm = new Block('bookmarks/updateForm.inc', ['type'=>'report']);
				echo $bookmarkForm->render('html', $this->template);
			}
		}
		echo '</div>';
	?>
	<span>
	<?php
		$helper = $this->template->getHelper('buttonLink');

		if (!empty($_GET['enteredDate']['start']) || !empty($_GET['enteredDate']['end'])) {
			$deleteURL = clone $currentURL;
			unset($deleteURL->enteredDate);

			$s = !empty($_GET['enteredDate']['start']) ? $_GET['enteredDate']['start'] : '*';
			$e = !empty($_GET['enteredDate']['end'])   ? $_GET['enteredDate']['end']   : '*';
			echo $helper->buttonLink($deleteURL, "$s to $e", 'delete');
		}
		if (!empty($_GET['departments'])) {
			foreach (array_keys($_GET['departments']) as $id) {
				try {
					$d = new Department($id);
					$deleteURL = clone $currentURL;
					unset($deleteURL->parameters['departments'][$id]);
					$name = self::escape($d->getName());
					echo $helper->buttonLink($deleteURL, $name, 'delete');
				}
				catch (\Exception $e) {
				}
			}
		}
		if (!empty($_GET['categories'])) {
			foreach (array_keys($_GET['categories']) as $id) {
				try {
					$c = new Category($id);
					$deleteURL = clone $currentURL;
					unset($deleteURL->parameters['categories'][$id]);
					$name = self::escape($c->getName());
					echo $helper->buttonLink($deleteURL, $name, 'delete');
				}
				catch (\Exception $e) {
				}
			}
		}
		if (!empty($_GET['postingPermissionLevel'])) {
            $deleteURL = clone $currentURL;
            unset($deleteURL->parameters['postingPermissionLevel']);
            $name = self::escape($_GET['postingPermissionLevel']);
            echo $helper->buttonLink($deleteURL, $name, 'delete');
		}
		if (!empty($_GET['clients'])) {
            foreach (array_keys($_GET['clients']) as $id) {
                try {
                    $c = new Client($id);
                    $deleteURL = clone $currentURL;
                    unset($deleteURL->parameters['clients'][$id]);
                    $name = self::escape($c->getName());
                    echo $helper->buttonLink($deleteURL, $name, 'delete');
                }
                catch (\Exception $e) {
                }
            }
		}

		foreach (AddressService::$customFieldDescriptions as $f=>$v) {
            if (!empty($_GET[$f])) {
                $deleteURL = clone $currentURL;
                unset($deleteURL->parameters[$f]);
                $name = self::escape($_GET[$f]);
                echo $helper->buttonLink($deleteURL, $name, 'delete');
            }
		}
	?>
	</span>
</div>
