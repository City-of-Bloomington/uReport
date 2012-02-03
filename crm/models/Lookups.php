<?php
/**
 * Class for working with various minor lookup tables for the system.
 *
 * These are sets of fields that are not important enough to warrant
 * full collections for themselves.  Instead, they're just arrays of
 * string values.
 *
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Lookups
{
	public static function get($fieldname)
	{
		$mongo = Database::getConnection();
		$result = $mongo->lookups->findOne(array('name'=>$fieldname));
		return !empty($result['items']) ? $result['items'] : array();
	}

	public static function save($fieldname,$items)
	{
		if (is_string($items)) {
			$items = explode(',',$items);
		}
		array_walk($items, function($value,$key) use(&$items) {
			$items[$key] = trim($value);
		});
		$mongo = Database::getConnection();
		$mongo->lookups->update(
			array('name'=>$fieldname),
			array('$set'=>array('items'=>$items)),
			array('upsert'=>true,'multiple'=>false,'safe'=>true)
		);
	}
}