<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Report
{
	private static $zend_db;
	private static $select;

	public static function assignments($get)
	{
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
				left join ticketHistory h on t.id=h.ticket_id and h.action_id=7
				$where
				group by t.assignedPerson_id, t.status, t.category_id,
					p.firstname, p.lastname,
					c.name,
					t.resolution_id, r.name
				order by t.assignedPerson_id, t.category_id, t.status, t.resolution_id";

		self::$zend_db = Database::getConnection();
		$result = self::$zend_db->fetchAll($sql);
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

	public static function categories($get)
	{
		$where = self::handleSearchParameters($get);
		$sql = "select c.id as category_id, c.name as category,
					t.assignedPerson_id, t.status, t.resolution_id,
					p.firstname, p.lastname, r.name as resolution,
					count(*) as count
				from categories         c
				     join tickets       t on c.id=t.category_id
				     join people        p on t.assignedPerson_id=p.id
				left join resolutions   r on t.resolution_id=r.id
				left join ticketHistory h on t.id=h.ticket_id and h.action_id=7
				$where
				group by c.id, c.name,
					t.assignedPerson_id, t.status, t.resolution_id,
					p.firstname, p.lastname,
					r.name
				order by c.id, t.assignedPerson_id, t.status desc, t.resolution_id
		";
		self::$zend_db = Database::getConnection();
		$d = array();
		$result = self::$zend_db->fetchAll($sql);
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
}
