<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Models;

use Blossom\Classes\Database;

class Metrics
{
    /**
     * @param  int $category_id
     * @param  int $numDays
     * @return int
     */
    public static function onTimePercentage($category_id, $numDays)
    {
        $zend_db = Database::getConnection();
        $sql = "select floor((sum(if (datediff(ifnull(t.closedDate, now()), t.enteredDate) <= c.slaDays, 1, 0)) / count(*)) * 100) as onTime
                from tickets t join categories c on t.category_id=c.id
                where c.id=? and t.enteredDate > (now() - interval ? day)";
        $result = $zend_db->query($sql)->execute([(int)$category_id, (int)$numDays]);
        if (count($result)) {
            $row = $result->current();
            return $row['onTime'];
        }
    }
}
