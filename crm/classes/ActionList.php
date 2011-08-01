<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class ActionList
{
	private static $actions;

	public static function initialize()
	{
		if (!self::$actions) {
			self::$actions = array();
			$mongo = Database::getConnection();
			foreach ($mongo->actions->find() as $data) {
				self::$actions[] = new Action($data);
			}
		}
	}

	public static function getActions()
	{
		self::initialize();
		return self::$actions;
	}
}