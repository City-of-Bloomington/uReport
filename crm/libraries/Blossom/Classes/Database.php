<?php
/**
 * Singleton for the Database connection
 *
 * @copyright 2006-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Blossom\Classes;
use Zend\Db\Adapter\Adapter;

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
			try {
				$parameters = array('driver'  =>DB_ADAPTER,
									'hostname'=>DB_HOST,
									'username'=>DB_USER,
									'password'=>DB_PASS,
									'database'  =>DB_NAME,
									'charset' =>'utf8');
				self::$connection = new Adapter($parameters);
			}
			catch (Exception $e) {
				die($e->getMessage());
			}
		}
		return self::$connection;
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
		}

	}
}
