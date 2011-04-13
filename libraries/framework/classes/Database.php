<?php
/**
 * Singleton for the Database connection
 *
 * @copyright 2006-2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Database
{
	private static $connection;

	/**
	 * @param boolean $reconnect If true, drops the connection and reconnects
	 * @return resource
	 */
	public static function getConnection($reconnect=false)
	{
		if ($reconnect) {
			self::$connection=null;
		}
		if (!self::$connection) {
			$parameters = array('connect'=>true);
			if (defined('DB_USER')) {
				$parameters['username'] = DB_USER;
				$parameters['password'] = DB_PASS;
			}
			try {
				self::$connection = new Mongo(DB_ADAPTER.'://'.DB_HOST);
			}
			catch (Exception $e) {
				die($e->getMessage());
			}
		}
		$db = DB_NAME;
		return self::$connection->$db;
	}

	/**
	 * Returns the type of database that's being used (mysql, oracle, etc.)
	 *
	 * @return string
	 */
	public static function getType()
	{
		switch (strtolower(DB_ADAPTER)) {
			case 'pdo_mysql':
			case 'mysqli':
				return 'mysql';
				break;

			case 'pdo_oci':
			case 'oci8':
				return 'oracle';
				break;
			default:
				return strtolower(DB_ADAPTER);
		}
	}
}
