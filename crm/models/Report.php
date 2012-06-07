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
		self::$zend_db = Database::getConnection();
		self::$select = self::$zend_db->select();
		self::$select->from(array('t'=>'tickets'), array(
			't.assignedPerson_id', 't.status',
			'count'=>'count(*)',
			'seconds'=>'avg(time_to_sec(timediff(ifnull(h.actionDate, now()), t.enteredDate)))'
		));
		self::$select->join(
			array('p'=>'people'),
			't.assignedPerson_id=p.id',
			array('p.firstname','p.lastname')
		);
		self::$select->joinLeft(array('h'=>'ticketHistory'), 't.id=h.ticket_id and h.action_id=7', array());
		self::$select->group(array('t.assignedPerson_id','t.status','p.firstname','p.lastname'));

		self::handleSearchParameters($get);

		$result = self::$zend_db->fetchAll(self::$select);
		$o = array();
		foreach ($result as $row) {
			$id = $row['assignedPerson_id'];
			$o[$id]['firstname'] = $row['firstname'];
			$o[$id]['lastname']  = $row['lastname'];
			$o[$id][$row['status']] = array('count'=>$row['count'], 'seconds'=>$row['seconds']);
		}
		return $o;
	}

	public static function categories($get)
	{
	}

	/**
	 * @param array $get
	 */
	private static function handleSearchParameters($get)
	{
		if (!empty($get['enteredDate'])) {
			$start = !empty($get['enteredDate']['start'])
				? date(ActiveRecord::MYSQL_DATE_FORMAT, strtotime($get['enteredDate']['start']))
				: '1970-01-01';
			$end = !empty($get['enteredDate']['end'])
				? date(ActiveRecord::MYSQL_DATE_FORMAT, strtotime($get['enteredDate']['end']))
				: date(ActiveRecord::MYSQL_DATE_FORMAT);
			self::$select->where('t.enteredDate<=?', array($end));
			self::$select->where('ifnull(h.actionDate, now())>=?', array($start));
		}
		if (!empty($get['departments'])) {
			$ids = array();
			foreach (array_keys($get['departments']) as $i) { $ids[] = (int)$i; }
			$ids = implode(',', $ids);
			self::$select->where("p.department_id in ($ids)");
		}
		if (!empty($get['categories'])) {
			$ids = array();
			foreach (array_keys($get['categories']) as $i) { $ids[] = (int)$i; }
			$ids = implode(',', $ids);
			self::$select->where("t.category_id in ($ids)");
		}
	}
}
