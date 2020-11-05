<?php
/**
 * Displays history without displaying information about people
 *
 * @copyright 2020 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param array  $this->history
 * @param Ticket $this->ticket          (optional)
 * @param bool   $this->disableComments (optional)
 */
declare (strict_types=1);
use Application\Models\Action;
foreach ($this->history as $event) {
    echo "
    <article class=\"historyItem\">
        <header>
            <h1>{$event->getActionDate(DATETIME_FORMAT)}
                {$event->getAction()->getName()}
            </h1>
        </header>
    </article>
    ";
}
