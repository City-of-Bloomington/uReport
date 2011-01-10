<?php
/**
 * @copyright 2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class LDAP implements ExternalAuthentication
{
	/**
	 * @param string $username
	 * @param string $password
	 * @throws Exception
	 */
	public static function authenticate($username,$password)
	{
		$connection = ldap_connect(LDAP_SERVER) or die("Couldn't connect to LDAP");
		ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_bind($connection);

		$result = ldap_search($connection,LDAP_DN,LDAP_USERNAME_ATTRIBUTE."=$username");
		if (ldap_count_entries($connection,$result)) {
			$entries = ldap_get_entries($connection, $result);

			if (preg_match("/^\{crypt\}(.+)/i",$entries[0][LDAP_PASSWORD_ATTRIBUTE][0],$matches)) {
				$ldapPassword = $matches[1];
				$salt = substr($ldapPassword,0,2);

				$encryptedPassword = crypt($password,$salt);
				if ($encryptedPassword === $ldapPassword) {
					return true;
				}
				else {
					throw new Exception('wrongPassword');
				}
			}
			else {
				throw new Exception("passwordIsCorrupted");
			}
		}
		else {
			throw new Exception("unknownUser");
		}
	}

	/**
	 * Saves a user's password to the LDAP server
	 *
	 * @param string $username
	 * @param string $password
	 */
	public static function savePassword($username,$password)
	{
		$connection = ldap_connect(LDAP_SERVER);
		ldap_set_option($connection,LDAP_OPT_PROTOCOL_VERSION,3);
		ldap_bind($connection,
				  LDAP_USERNAME_ATTRIBUTE."=".LDAP_ADMIN_USER.",o=".LDAP_DOMAIN,
				  LDAP_ADMIN_PASS) or die(ldap_error($connection));

		$result = ldap_search($connection,LDAP_DN,LDAP_USERNAME_ATTRIBUTE."=$username");
		$entries = ldap_get_entries($connection, $result);

		$dn = LDAP_USERNAME_ATTRIBUTE."=$username,ou=people,o=".LDAP_DOMAIN;
		if ($this->getPassword()) {
			$salt = substr(md5(time()),0,2);
			$encryptedPassword = "{CRYPT}".crypt($password,$salt);

			$password = array(LDAP_PASSWORD_ATTRIBUTE=>$encryptedPassword);

			if (isset($entries[0][LDAP_PASSWORD_ATTRIBUTE])) {
				// Modify
				ldap_mod_replace($connection,$dn,$password)
					or die(print_r($password).ldap_error($connection));
			}
			else {
				// Add
				ldap_mod_add($connection,$dn,$password)
					or die(print_r($password).ldap_error($connection));
			}
		}
		else {
			// Delete
			$password = array();
			ldap_mod_del($connection,$dn,$password)
				or die(print_r($password).ldap_error($connection));
		}
	}
}
