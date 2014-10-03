<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Zend\Db\Result $this->data
 * @param string $this->title
 */
use Blossom\Classes\View;
?>
<div>
<h2><?php echo View::escape($this->title); ?></h2>
<table>
    <thead>
        <tr><th>Category ID</th>
            <th>Category</th>
            <th>Count</th>
    </thead>
    <tbody>
    <?php
        foreach ($this->data as $row) {
            $name = View::escape($row['category']);
            echo "
            <tr><td>$row[category_id]</td>
                <td>$name</td>
                <td>$row[count]</td>
            </tr>
            ";
        }
    ?>
    </tbody>
</table>
</div>
