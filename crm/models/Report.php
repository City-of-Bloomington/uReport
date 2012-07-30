<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Report
{
	private static $select;
	private static $closedAction;

	/**
	 * @return int
	 */
	public static function closedId()
	{
		if (!self::$closedAction) { self::$closedAction = new Action('close'); }
		return self::$closedAction->getId();
	}

	/**
	 * @return array
	 */
	public static function assignments($get)
	{
		$closed = self::closedId();

		$where = self::handleSearchParameters($get);
		$sql = "select t.assignedPerson_id, t.status, t.category_id,
					p.firstname, p.lastname,
					c.name as category,
					t.resolution_id, r.name as resolution,
					count(*) as count
				from tickets t
				     join people        p on t.assignedPerson_id=p.id
				left join categories    c on t.category_id=c.id
				left join resolutions   r on t.resolution_id=r.id
				left join ticketHistory h on t.id=h.ticket_id and h.action_id=$closed
				$where
				group by t.assignedPerson_id, t.status, t.category_id,
					p.firstname, p.lastname,
					c.name,
					t.resolution_id, r.name
				order by p.lastname, p.firstname, c.name, t.status, t.resolution_id";

		$zend_db = Database::getConnection();
		$result = $zend_db->fetchAll($sql);
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
	 * @return array
	 */
	public static function categories($get)
	{
		$closed = self::closedId();
		$where = self::handleSearchParameters($get);
		$sql = "select c.id as category_id, c.name as category,
					t.assignedPerson_id, t.status, t.resolution_id,
					p.firstname, p.lastname, r.name as resolution,
					count(*) as count
				from categories         c
				     join tickets       t on c.id=t.category_id
				     join people        p on t.assignedPerson_id=p.id
				left join resolutions   r on t.resolution_id=r.id
				left join ticketHistory h on t.id=h.ticket_id and h.action_id=$closed
				$where
				group by c.id, c.name,
					t.assignedPerson_id, t.status, t.resolution_id,
					p.firstname, p.lastname,
					r.name
				order by c.name, p.lastname, p.firstname, t.status desc, t.resolution_id
		";
		$zend_db = Database::getConnection();
		$d = array();
		$result = $zend_db->fetchAll($sql);
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
			$start = !empty($get['enteredDate']['start'])
				? date(ActiveRecord::MYSQL_DATE_FORMAT, strtotime($get['enteredDate']['start']))
				: '1970-01-01';
			$end = !empty($get['enteredDate']['end'])
				? date(ActiveRecord::MYSQL_DATE_FORMAT, strtotime($get['enteredDate']['end']))
				: date(ActiveRecord::MYSQL_DATE_FORMAT);
			$options[] = "(t.enteredDate<='$end' and ifnull(h.actionDate, now())>='$start')";
		}
		if (!empty($get['departments'])) {
			$ids = array();
			foreach (array_keys($get['departments']) as $i) { $ids[] = (int)$i; }
			$ids = implode(',', $ids);
			$options[] = "p.department_id in ($ids)";
		}
		if (!empty($get['categories'])) {
			$ids = array();
			foreach (array_keys($get['categories']) as $i) { $ids[] = (int)$i; }
			$ids = implode(',', $ids);
			$options[] = "t.category_id in ($ids)";
		}
		if ($options) {
			$options = implode(' and ', $options);
			return "where $options";
		}
	}

	public static function currentOpenTickets()
	{
		$sql = "select t.category_id, c.name as category, sum(status='open') as open
				from tickets t
				join categories c on t.category_id=c.id
				group by t.category_id
				having open>0
				order by open";
		$zend_db = Database::getConnection();
		return $zend_db->fetchAll($sql);
	}

	/**
	 * Returns the tickets opened in the past 24 hours
	 *
	 * @return array
	 */
	public static function openedTickets()
	{
		$sql = "select t.category_id, c.name as category, sum(status='open') as open
				from tickets t
				join categories c on t.category_id=c.id
				where t.enteredDate > (now() - interval 1 day)
				group by t.category_id
				having open>0
				order by open";
		$zend_db = Database::getConnection();
		return $zend_db->fetchAll($sql);
	}

	/**
	 * Returns the tickets closed in the past 24 hours
	 *
	 * @return array
	 */
	public static function closedTickets()
	{
		$closed = self::closedId();
		$sql = "select t.category_id, c.name as category, sum(status='closed') as closed
				from tickets t
				join categories c on t.category_id=c.id
				join ticketHistory h on t.id=h.ticket_id and h.action_id=$closed
				where h.actionDate > (now() - interval 1 day)
				group by t.category_id
				order by closed";
		$zend_db = Database::getConnection();
		return $zend_db->fetchAll($sql);
	}

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
		return $zend_db->fetchAll($sql);
	}

	public static function closedTicketsCount($start, $end)
	{
		$closed = self::closedId();
		$s = date(ActiveRecord::MYSQL_DATE_FORMAT, strtotime($start));
		$e = date(ActiveRecord::MYSQL_DATE_FORMAT, strtotime($end));
		$sql = "select date_format(h.actionDate, '%Y-%m-%d') as date, count(*) as closed
				from tickets t
				join ticketHistory h on t.id=h.ticket_id and h.action_id=$closed
				where '$s'<=h.actionDate and h.actionDate<='$e'
				group by date
				order by date";
		$zend_db = Database::getConnection();
		return $zend_db->fetchAll($sql);
	}

	public static function categoryActivity()
	{
		$closed = self::closedId();
		$sql = "select c.name,
					sum(t.status='open') as currentopen,
					sum((now() - interval 1 day  ) <= t.enteredDate) as openedday,
					sum((now() - interval 1 week ) <= t.enteredDate) as openedweek,
					sum((now() - interval 1 month) <= t.enteredDate) as openedmonth,
					sum((now() - interval 1 day  ) <= h.actionDate ) as closedday,
					sum((now() - interval 1 week ) <= h.actionDate ) as closedweek,
					sum((now() - interval 1 month) <= h.actionDate ) as closedmonth,
					floor(avg(datediff(ifnull(h.actionDate, now()),t.enteredDate))) as days
				from tickets t
				join categories c on t.category_id=c.id
				left join ticketHistory h on t.id=h.ticket_id and h.action_id=$closed
				group by t.category_id
				order by currentopen desc";
		$zend_db = Database::getConnection();
		return $zend_db->fetchAll($sql);
	}
}
