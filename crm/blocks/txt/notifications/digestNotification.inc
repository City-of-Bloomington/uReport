<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Person $this->person
 */
$name = View::escape($this->person->getFullname());
?>
<?php echo $name; ?>, here is your weekly summary of open cases in <?php echo APPLICATION_NAME; ?>.  Please visit your profile to edit or update your cases.

<?php echo $this->person->getURL(); ?>


