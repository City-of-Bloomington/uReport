<?php
/**
 * @copyright 2013-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Person $this->person
 */
$name = self::escape($this->person->getFullname());
?>
<p>
<?php
	echo sprintf($this->_('digestNotification', 'messages'),
		$name,
		APPLICATION_NAME,
		$this->person->getURL()
	);
?>
</p>
