<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Person $this->person
 */
$name = self::escape($this->person->getFullname());
?>
<?= $name; ?>, here is your weekly summary of open cases in <?= APPLICATION_NAME; ?>.  Please visit your profile to edit or update your cases.

<?= $this->person->getURL(); ?>


