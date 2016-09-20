<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param stdClass $this->notification
 */
use Application\Models\Person;

$this->template->addToAsset('scripts', JQUERY.'/jquery.min.js');
$this->template->addToAsset('scripts', BASE_URI.'/js/collapsible.js');

$people = [];
foreach ($this->notification->people as $id) {
    try {
        $person = new Person($id);
        $people[] = self::escape($person->getFullname());
    }
    catch (\Exception $e) { }

    $people = implode(', ', $people);
}
?>
<article class="sentNotification collapsible">
    <h1><?= sprintf($this->_('sentNotification_to', 'messages'), $people); ?></h1>
    <div class="message"><?= self::escape($this->notification->message); ?></div>
</article>