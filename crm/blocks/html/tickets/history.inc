<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param array $this->history
 * @param bool $this->disableComments
 */
use Application\Models\Person;
?>
<section class="history">
    <header>
        <h1><?= $this->_('labels.history'); ?></h1>
    </header>
	<?php
		foreach ($this->history as $event) {
            $description = self::escape($event->getDescription($this->template));

			echo "
			<article class=\"historyItem\">
				<h1>{$event->getActionDate(DATE_FORMAT)} $description</h1>
			";

			if (!$this->disableComments) {
                $notes = self::escape($event->getNotes());
				echo "<p>$notes</p>";
			}

			echo "</article>";
		}
	?>
</section>
