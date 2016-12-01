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
                <tr><th></th>
                    <th><?= $this->_('open'   ); ?></th>
                    <th><?= $this->_('closed' ); ?></th>
                    <th>Average days responsible</th>
                </tr>
            </thead>
            <tbody>
            <?php
                $current_url = new Url(Url::current_url());
                $data_url    = new Url(BASE_URL.'/reports/person');
                $data_url->parameters = $current_url->parameters;

                foreach ($this->data as $row) {
					$data_url->actionPerson_id = $row['actionPerson_id'];

                    echo "
                    <tr><td><a href=\"$data_url\">$row[firstname] $row[lastname]</a></td>
						<td>$row[open]</td>
						<td>$row[closed]</td>
                        <td>$row[average]</td>
                    </tr>
                    ";
                }
            ?>
            </tbody>
        </table>
    </header>
</section>
