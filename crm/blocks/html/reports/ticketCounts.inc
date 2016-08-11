<?php
/**
 * @copyright 2014-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Zend\Db\Result $this->data
 * @param string         $this->title
 */
?>
<section>
    <header>
        <h1><?= self::escape($this->title); ?></h1>
    </header>
    <table>
        <thead>
            <tr><th><?= $this->_('id'); ?></th>
                <th><?= $this->_('category'); ?></th>
                <th><?= $this->_('count'); ?></th>
        </thead>
        <tbody>
        <?php
            foreach ($this->data as $row) {
                $name = self::escape($row['category']);
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
</section>