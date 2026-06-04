<?php
/**
 * @copyright 2012-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param string $this->location
 */
use Application\Models\Person;
use Application\Models\PersonTable;

if ($this->location
    && Person::isAllowed('people', 'view')) {
    
    echo '<div class="locationPeople">';
    $table = new PersonTable();
    $people = $table->find(['address' => $this->location]);
    if ($people['rows']) {
        $title = $this->_(['person', 'people', $people['total']]);
        echo "
        <h2>$title</h2>
        <p>{$this->_('locationPeople_description', 'messages')}</p>
        <ul>
        ";
        foreach ($people['rows'] as $person) {
            $name = self::escape($person->getFullname());
            echo "<li><a href=\"{$person->getURL()}\">$name</a></li>";
        }
        echo "</ul>";
    }

    echo '</div>';
}
