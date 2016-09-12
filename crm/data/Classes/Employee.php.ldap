<?php
/**
 * A example class for working with entries in LDAP.
 *
 * This class is written specifically for the City of Bloomington's
 * LDAP layout.  If you are going to be doing LDAP authentication
 * with your own LDAP server, you will probably need to customize
 * the fields used in this class.
 *
 * To implement your own identity class, you should create a class
 * in SITE_HOME/Classes.  The SITE_HOME directory does not get
 * overwritten during an upgrade.  The namespace for your class
 * should be Site\Classes\
 *
 * You can use this class as a starting point for your own implementation.
 * You will ned to change the namespace to Site\Classes.  You might also
 * want to change the name of the class to suit your own needs.
 *
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Site\Classes;

use Blossom\Classes\ExternalIdentity;

class Employee implements ExternalIdentity
{
	private static $connection;
	private static $config;
	private $entry;

	private static function getConfig()
	{
        global $DIRECTORY_CONFIG;

        if (!self::$config) {
             self::$config = $DIRECTORY_CONFIG['Employee'];
        }
        return self::$config;
	}

	/**
	 * @param array $config
	 * @param string $username
	 * @param string $password
	 * @throws Exception
	 */
	public static function authenticate($username, $password)
	{
        $config = self::getConfig();

		$bindUser = sprintf(str_replace('{username}','%s',$config['DIRECTORY_USER_BINDING']),$username);

		$connection = ldap_connect($config['DIRECTORY_SERVER']) or die("Couldn't connect to ADS");
		ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
		if (ldap_bind($connection,$bindUser,$password)) {
			return true;
		}
	}


	/**
	 * Loads an entry from the LDAP server for the given user
	 *
	 * @param array $config
	 * @param string $username
	 */
	public function __construct($username)
	{
        $config = self::getConfig();

		$this->openConnection();

		$result = ldap_search(
			self::$connection,
			$config['DIRECTORY_BASE_DN'],
			$config['DIRECTORY_USERNAME_ATTRIBUTE']."=$username"
		);
		if (ldap_count_entries(self::$connection,$result)) {
			$entries = ldap_get_entries(self::$connection, $result);
			$this->entry = $entries[0];
		}
		else {
			throw new \Exception('ldap/unknownUser');
		}
	}

	/**
	 * Creates the connection to the LDAP server
	 */
	private function openConnection()
	{
        $config = self::getConfig();

		if (!self::$connection) {
			if (self::$connection = ldap_connect($config['DIRECTORY_SERVER'])) {
				ldap_set_option(self::$connection, LDAP_OPT_PROTOCOL_VERSION,3);
				ldap_set_option(self::$connection, LDAP_OPT_REFERRALS, 0);
				if (!empty($config['DIRECTORY_ADMIN_BINDING'])) {
					if (!ldap_bind(
							self::$connection,
							$config['DIRECTORY_ADMIN_BINDING'],
							$config['DIRECTORY_ADMIN_PASS']
						)) {
						throw new \Exception(ldap_error(self::$connection));
					}
				}
				else {
					if (!ldap_bind(self::$connection)) {
						throw new \Exception(ldap_error(self::$connection));
					}
				}
			}
			else {
				throw new \Exception(ldap_error(self::$connection));
			}
		}
	}

	/**
	 * @return string
	 */
	public function getUsername()	{ return $this->get('cn'); }
	public function getFirstname()	{ return $this->get('givenname'); }
	public function getLastname()	{ return $this->get('sn'); }
	public function getEmail()		{ return $this->get('mail'); }
	public function getPhone()		{ return $this->get('telephonenumber'); }
	public function getAddress()	{ return $this->get('postaladdress'); }
	public function getCity()		{ return $this->get('l'); }
	public function getState()		{ return $this->get('st'); }
	public function getZip()		{ return $this->get('postalcode'); }

	/**
	 * Returns the first scalar value from the entry's field
	 *
	 * @param string $field
	 * @return string
	 */
	private function get($field) {
		return isset($this->entry[$field][0]) ? $this->entry[$field][0] : '';
	}
}
