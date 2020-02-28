<?php
/**
 * @copyright 2016-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param stdClass $this->notification
 */
use Application\Models\Person;

$people = [];
foreach ($this->notification->people as $id) {
    try {
        $person = new Person($id);
        $people[] = self::escape($person->getFullname());
    }
    catch (\Exception $e) { }
}
$people = implode(', ', $people);
?>
<details class="sentNotification">
    <summary><?= sprintf($this->_('sentNotification_to', 'messages'), $people); ?></summary>
    <div class="message"><?= self::escape($this->notification->message); ?></div>
</details>
