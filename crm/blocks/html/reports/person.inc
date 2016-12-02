<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param array $this->data
 */
use Blossom\Classes\Url;

$this->_include('reports/parameters.inc');
?>
<section class="searchResults">
    <header>
        <h1><?= $this->_('staff'); ?></h1>

        <table>
            <thead>
                <tr><th><?= $this->_('ticket'); ?></th>
                    <th></th>
                    <th>Days Responsible</th>
                    <th><?= $this->_('assignedDate'); ?></th>
                    <th><?= $this->_('closedDate'); ?></th>
                    <th></th>
                    <th><?= $this->_('assignedPerson_id'); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php
                foreach ($this->data as $row) {
                    $uri = BASE_URI."/tickets/view?ticket_id=$row[ticket_id]";
                    $actionDate = new \DateTime($row['actionDate']);
                    $closedDate = '';
                    if ($row['closedDate']) {
                        $d = new \DateTime($row['closedDate']);
                        $closedDate = $d->format(DATE_FORMAT);
                    }
                    echo "
                    <tr><td><a href=\"$uri\">$row[ticket_id]</a></td>
                        <td>$row[firstname] $row[lastname]</td>
                        <td>$row[days]</td>
                        <td>{$actionDate->format(DATE_FORMAT)}</td>
                        <td>$closedDate</td>
                        <td>$row[category]</td>
                        <td>$row[assigned_firstname] $row[assigned_lastname]</td>
                    </tr>
                    ";
                }
            ?>
            </tbody>

        </table>
    </header>
</section>
