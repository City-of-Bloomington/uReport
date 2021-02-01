<?php
/**
 * @copyright 2011-2021 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param array  $this->history
 * @param Ticket $this->ticket          (optional)
 * @param bool   $this->disableComments (optional)
 */
use Application\Models\Action;
use Application\Models\Person;
?>
<section class="history">
    <header>
        <h1><?= $this->_('history'); ?></h1>
    </header>
	<?php
        $canViewPeople = Person::isAllowed('people', 'view');

        foreach ($this->history as $event) {
            $description = $event->getDescription($this->template);
            echo "
            <article class=\"historyItem\">
                <header>
                    <h1>{$event->getActionDate(DATETIME_FORMAT)} $description</h1>
                </header>
            ";

            if (!$this->disableComments) {
                $notes = self::escape($event->getNotes());
                if ($notes) {
                    echo "<p>$notes</p>";
                }
            }
            switch ($event->getAction()->getName()) {
                case Action::UPDATED:
                    if ($event->getData()) { echo json_encode($event->getData()); }
                break;
            }

            $notification = $event->getSentNotifications();
            if ($notification) {
                $this->notification = $notification;
                $this->_include('ticketHistory/sentNotification.inc');
            }
            echo "</article>";
        }
	?>
</section>
