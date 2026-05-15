<?php
/**
 * @copyright 2016-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models;

use Application\ActiveRecord;
use Application\Database;

class Metrics
{
    public static function onTimePercentage($category_id, $numDays, ?\DateTime $effectiveDate=null): ?int
    {
        $category_id = (int)$category_id;
        $numDays     = (int)$numDays;
        if (!$effectiveDate) { $effectiveDate = new \DateTime(); }

        $s = clone $effectiveDate;
        $e = clone $effectiveDate;

        $s->sub(new \DateInterval("P{$numDays}D"));
        $scopeStart = $s->format(ActiveRecord::MYSQL_DATETIME_FORMAT);
        $scopeEnd   = $e->format(ActiveRecord::MYSQL_DATETIME_FORMAT);
        $scopeFilter = "('$scopeStart' <= ifnull(closedDate, now()) and '$scopeEnd' >= enteredDate)";

        $sql = "select x.total, x.ontime, x.effectiveDate, floor(ontime / total * 100) as percentage
                from (
                    select  count(*) as total,
                            max(t.lastModified) as effectiveDate,
                            sum(if (datediff(ifnull(t.closedDate, now()), t.enteredDate) <= c.slaDays, 1, 0)) as ontime
                    from tickets t
                    join categories c on t.category_id=c.id
                    where t.category_id=?
                    and (? <= ifnull(closedDate, now()) and ? >= enteredDate)
                ) x";
        $result = Database::query($sql, [$category_id, $scopeStart, $scopeEnd]);
        if (count($result)) {
            return (int)$result[0];
        }
        return null;
    }
}
