<?php
/**
 * @copyright 2012-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;
use Blossom\Classes\ActiveRecord;
use Blossom\Classes\Database;

class Report
{
	private static $select;
	private static $closedAction;

	/**
	 * @return int
	 */
	public static function closedId()
	{
		if (!self::$closedAction) { self::$closedAction = new Action('closed'); }
		return self::$closedAction->getId();
	}

	/**
	 * @param array $get The raw GET request
	 * @return array
	 */
	public static function assignments($get)
	{
		$closed = self::closedId();
        $options = self::handleSearchParameters($get);
        $where = $options ? "where $options" : '';

		$sql = "select t.assignedPerson_id, t.status, t.category_id,
					p.firstname, p.lastname,
					c.name as category,
					t.substatus_id, s.name as substatus,
					count(*) as count
				from tickets t
					 join people        p on t.assignedPerson_id=p.id
				left join categories    c on t.category_id=c.id
				left join substatus     s on t.substatus_id=s.id
				$where
				group by t.assignedPerson_id, t.status, t.category_id,
					p.firstname, p.lastname,
					c.name,
					t.substatus_id, s.name
				order by p.lastname, p.firstname, c.name, t.status, t.substatus_id";

		$zend_db = Database::getConnection();
		$result = $zend_db->query($sql)->execute();
		$d = array();
		foreach ($result as $r) {
			$pid = $r['assignedPerson_id'];
			$cid = $r['category_id'];
			if (!isset($d[$pid])) { $d[$pid]['name'] = "$r[firstname] $r[lastname]"; }
			if (!isset($d[$pid]['categories'][$cid])) {
				$d[$pid]['categories'][$cid] = array(
					'name'=>$r['category'],
					'data'=>array()
				);
			}
			$d[$pid]['categories'][$cid]['data'][] = $r;
		}
		return $d;
	}

	/**
	 * @param array $get The raw GET request
	 * @return array
	 */
	public static function categories($get)
	{
		$closed = self::closedId();
		$options = self::handleSearchParameters($get);
		$where = $options ? "where $options" : '';

		$sql = "select c.id as category_id, c.name as category,
					t.assignedPerson_id, t.status, t.substatus_id,
					p.firstname, p.lastname, s.name as substatus,
					count(*) as count
				from categories     c
					 join tickets   t on c.id=t.category_id
					 join people    p on t.assignedPerson_id=p.id
				left join substatus s on t.substatus_id=s.id
				$where
				group by c.id, c.name,
					t.assignedPerson_id, t.status, t.substatus_id,
					p.firstname, p.lastname,
					s.name
				order by c.name, p.lastname, p.firstname, t.status desc, t.substatus_id";
		$zend_db = Database::getConnection();
		$d = array();
		$result = $zend_db->query($sql)->execute();
		foreach ($result as $r) {
			$cid = $r['category_id'];
			$pid = $r['assignedPerson_id'];
			if (!isset($d[$cid])) { $d[$cid]['name'] = $r['category']; }
			if (!isset($d[$cid]['people'][$pid])) {
				$d[$cid]['people'][$pid] = array(
					'name'=>"$r[firstname] $r[lastname]",
					'data'=> array()
				);
			}
			$d[$cid]['people'][$pid]['data'][] = $r;
		}
		return $d;
	}

	/**
	 * Creates a comma-separated list of numbers from a request parameter
	 *
	 * The report form includes checkboxes for choosing categories and
	 * departments to include in the report.  The form posts these inputs
	 * as an array, using the ID as the index.  When a user checks on of
	 * them, the id will be added to the associated $_REQUEST array
	 * $_REQUEST['categories'][$id] = "On"
	 * $_REQUEST['departments'][$id] = "On"
	 *
	 * Checkboxes that are unchecked will not exist in the request parameters,
	 * so you should never see one set to "Off".  They're either "On" or
	 * they're just not there.
	 *
	 * For the SQL select string, we need to convert all the ID numbers into
	 * a safe, comma-separated string of ID numbers.
	 *
	 * @param $requestParam
	 * @return string
	 */
	private static function implodeIds($requestParam)
	{
		$ids = array();
		foreach (array_keys($requestParam) as $i) { $ids[] = (int)$i; }
		return implode(',', $ids);
	}

	private static function parseDate($string)
	{
        try {
            $d = \DateTime::createFromFormat(DATE_FORMAT, $string);
            if ($d) {
                return $d->format(ActiveRecord::MYSQL_DATE_FORMAT);
            }
        }
        catch (\Exception $e) {
        }
	}

	/**
	 * WARNING:
	 * Be very careful here, we're handling SQL as raw strings for
	 * both maintainability and performance reasons.
	 * Make sure nothing from the $get array is used in a string.
	 * Everything must be cleaned up before using in the where string
	 *
	 * @param array $get
	 * @return string SQL for the where portion of a select
	 */
	private static function handleSearchParameters($get)
	{
		$options = array();
		if (!empty($get['enteredDate'])) {
            if (!empty($get['enteredDate']['start'])) {
                $start = self::parseDate($get['enteredDate']['start']);
            }
            $start = !empty($start) ? $start : '1970-01-01';

            if (!empty($get['enteredDate']['end'])) {
                $end = self::parseDate($get['enteredDate']['end']);
            }
            $end = !empty($end) ? $end : date(ActiveRecord::MYSQL_DATE_FORMAT);

			$options[] = "(t.enteredDate<='$end' and ifnull(t.closedDate, now())>='$start')";
		}
		self::handleFilters($options, $get);

		return $options ? implode(' and ', $options) : '';
	}

	private static function handleFilters(&$options, $get)
	{
        if (!empty($get['departments'])) {
            $ids = self::implodeIds($get['departments']);
            $options[] = "p.department_id in ($ids)";
        }
        if (!empty($get['categories'])) {
            $ids = self::implodeIds($get['categories']);
            $options[] = "t.category_id in ($ids)";
        }
        if (!empty($get['clients'])) {
            $ids = self::implodeIds($get['clients']);
            $options[] = "t.client_id in ($ids)";
        }
        if (!empty($get['postingPermissionLevel'])) {
            $v = $get['postingPermissionLevel']=='anonymous'
                ? 'anonymous'
                : 'staff';
            $options[] = "postingPermissionLevel='$v'";
        }
	}

    /**
     * The volume query wants tickets reported during the provided
     * date range.  This is different from the rest of the reports
     * that are looking for tickets that were active during the date
     * range.
     *
     * So, the date ranges get handled in a special way, but all the
     * other possible filters are handled the same.
     *
     * @param array $get The raw GET request
     * @return string The sql for the where portion of a select
     */
    private static function volumeOptions($get)
    {
        $options = [];
        if (!empty($get['enteredDate'])) {
            $start = !empty($get['enteredDate']['start'])
                ? date(ActiveRecord::MYSQL_DATE_FORMAT, strtotime($get['enteredDate']['start']))
                : '1970-01-01';
            $end = !empty($get['enteredDate']['end'])
                ? date(ActiveRecord::MYSQL_DATE_FORMAT, strtotime($get['enteredDate']['end']))
                : date(ActiveRecord::MYSQL_DATE_FORMAT);
            $options[] = "enteredDate between '$start' and '$end'";
        }
        self::handleFilters($options, $get);

        return count($options) ? implode(' and ', $options) : '';
    }

	/**
	 * @return Zend\Db\ResultSet
	 */
	public static function currentOpenTickets()
	{
		$sql = "select t.category_id, c.name as category, sum(status='open') as count
				from tickets t
				join categories c on t.category_id=c.id
				group by t.category_id
				having count>0
				order by count";
		$zend_db = Database::getConnection();
		return $zend_db->query($sql)->execute();
	}

	/**
	 * Returns the tickets opened in the past 24 hours
	 *
	 * @return Zend\Db\ResultSet
	 */
	public static function openedTickets()
	{
		$sql = "select t.category_id, c.name as category, sum(status='open') as count
				from tickets t
				join categories c on t.category_id=c.id
				where t.enteredDate > (now() - interval 1 day)
				group by t.category_id
				having count>0
				order by count";
		$zend_db = Database::getConnection();
		return $zend_db->query($sql)->execute();
	}

	/**
	 * Returns the tickets closed in the past 24 hours
	 *
	 * @return Zend\Db\ResultSet
	 */
	public static function closedTickets()
	{
		$closed = self::closedId();
		$sql = "select t.category_id, c.name as category, sum(status='closed') as count
				from tickets t
				join categories c on t.category_id=c.id
				where t.closedDate > (now() - interval 1 day)
				group by t.category_id
				order by count";
		$zend_db = Database::getConnection();
		return $zend_db->query($sql)->execute();
	}

	/**
	 * Returns tickets that were created during a date range
	 *
	 * Dates should be strings that are parseable by strtotime
	 *
	 * @param string $start
	 * @param string $end
	 * @return Zend\Db\ResultSet
	 */
	public static function openTicketsCount($start, $end)
	{
		$s = date(ActiveRecord::MYSQL_DATE_FORMAT, strtotime($start));
		$e = date(ActiveRecord::MYSQL_DATE_FORMAT, strtotime($end));
		$sql = "select date_format(t.enteredDate, '%Y-%m-%d') as date, count(*) as open
				from tickets t
				where '$s'<=t.enteredDate and t.enteredDate<='$e'
				group by date
				order by date";
		$zend_db = Database::getConnection();
		return $zend_db->query($sql)->execute();
	}

	/**
	 * Returns tickets that were closed during the provided date range
	 *
	 * Dates should be strings that are parseable by strtotime
	 *
	 * @param string $start
	 * @param string $end
	 * @return Zend\Db\ResultSet ('date'=>xx, 'closed'=>xx)
	 */
	public static function closedTicketsCount($start, $end)
	{
		$closed = self::closedId();
		$s = date(ActiveRecord::MYSQL_DATE_FORMAT, strtotime($start));
		$e = date(ActiveRecord::MYSQL_DATE_FORMAT, strtotime($end));
		$sql = "select date_format(t.closedDate, '%Y-%m-%d') as date, count(*) as closed
				from tickets t
				where '$s'<=t.closedDate and t.closedDate<='$e'
				group by date
				order by date";
		$zend_db = Database::getConnection();
		return $zend_db->query($sql)->execute();
	}

	/**
	 * @return Zend\Db\ResultSet
	 */
	public static function categoryActivity()
	{
		$closed = self::closedId();
		$sql = "select c.name,c.slaDays,
					sum(t.status='open') as currentopen,
					sum((now() - interval 1 day  ) <= t.enteredDate) as openedday,
					sum((now() - interval 1 week ) <= t.enteredDate) as openedweek,
					sum((now() - interval 1 month) <= t.enteredDate) as openedmonth,
					sum((now() - interval 1 day  ) <= t.closedDate ) as closedday,
					sum((now() - interval 1 week ) <= t.closedDate ) as closedweek,
					sum((now() - interval 1 month) <= t.closedDate ) as closedmonth,
					floor(avg(datediff(ifnull(t.closedDate, now()),t.enteredDate))) as days
				from tickets t
				join categories c on t.category_id=c.id
				group by t.category_id
				order by currentopen desc";
		$zend_db = Database::getConnection();
		return $zend_db->query($sql)->execute();
	}


	/**
	 * The number of tickets that were open on the date provided
	 *
	 * Dates should be strings in MySQL Date Format
	 *
	 * @param int $timestamp
	 * @param array $get The raw GET request
	 * @return int
	 */
	public static function outstandingTicketCount($date, $get)
	{
		$sql = "select count(t.id) as count
				from tickets t
				left join people p on t.assignedPerson_id=p.id
				where t.enteredDate<=? and (?<=t.closedDate or t.closedDate is null)";

		if (!empty($get['categories'])) {
			$ids = self::implodeIds($get['categories']);
			$sql.= " and t.category_id in ($ids)";
		}
		if (!empty($get['departments'])) {
			$ids = self::implodeIds($get['departments']);
			$sql.= " and p.department_id in ($ids)";
		}
		$zend_db = Database::getConnection();
		$result = $zend_db->query($sql)->execute([$date, $date]);
		$row = $result->current();
		return $row['count'];
	}

	/**
	 * Returns the average SLA percentage for tickets closed on the given date
	 *
	 * Dates should be strings in MySQL Date Format
	 *
	 * @param string $date
	 * @param array $get The raw GET request
	 * @return int
	 */
	public static function closedTicketsSlaPercentage($date, $get)
	{
		$sql = "select  round(avg(((datediff(t.closedDate, t.enteredDate) / c.slaDays) * 100))) as slaPercentage
                from  tickets    t
                join  categories c on t.category_id=c.id
                left join people p on t.assignedPerson_id=p.id
                where slaDays is not null
                  and ?<=closedDate and closedDate<=adddate(?, interval 1 day)";
		if (!empty($get['categories'])) {
			$ids = self::implodeIds($get['categories']);
			$sql.= " and t.category_id in ($ids)";
		}
		if (!empty($get['departments'])) {
			$ids = self::implodeIds($get['departments']);
			$sql.= " and p.department_id in ($ids)";
		}
		$zend_db = Database::getConnection();
		$result = $zend_db->query($sql)->execute([$date, $date]);
		$row = $result->current();
		return $row['slaPercentage'];
	}

	/**
	 * @param timestamp $start
	 * @param timestamp $end
	 * @return array
	 */
	public static function generateDateArray($start, $end)
	{
		$dates = array();
		while ($start <= $end) {
			$dates[] = date('Y-m-d', $start);
			$start = strtotime('+1 day', $start);
		}
		return $dates;
	}


	/**
	 * @param array $get The raw GET request
	 * @return array
	 */
	public static function volumeByDepartment($get)
	{
        $options = self::volumeOptions($get);
        $where = $options ? "where $options" : '';

        $zend_db = Database::getConnection();

        $sql = "select count(*) as count
                from tickets t
                join categories p on t.category_id=p.id
                $where";
        $result = $zend_db->query($sql)->execute();
        $row = $result->current();
        $totalCount = $row['count'];


        $sql = "select d.id, d.name, count(*) as count
                from departments d
                join categories p on d.id=p.department_id
                join tickets t on p.id=t.category_id
                $where
                group by d.id, d.name order by d.name";
        $result = $zend_db->query($sql)->execute();

        return [ 'totalCount'=>$totalCount, 'result'=>$result ];
	}


	public static function volumeByCategory($get, $department_id)
	{
        $options = self::volumeOptions($get);
        $options = $options ? "and $options" : '';

        $zend_db = Database::getConnection();

        $sql = "select p.name, count(*) as count
                from categories p
                join tickets t on p.id=t.category_id
                where p.department_id=?
                $options
                group by p.name order by count desc";
        $result = $zend_db->query($sql)->execute([$department_id]);
        return ['result'=>$result];
    }
}
