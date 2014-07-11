<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Person $this->person
 */
use Blossom\Classes\View;

$name = View::escape($this->person->getFullname());
?>
<p>
<?php
	echo sprintf($this->translate('text.digestNotification'),
		$name,
		APPLICATION_NAME,
		$this->person->getURL()
	);
?>
</p>
