<?php
/**
 * @copyright 2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
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
		if (ldap_bind($connection,LDAP_USERNAME_ATTRIBUTE."=$username,".LDAP_DN,"$password")) {
			return true;
		}
	}

	/**
	 * Encrypts and saves a user's password to LDAP
	 *
	 * @param string $username
	 * @param string $password Unencryped password string
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
		if ($password) {
			// We're going to use SSHA, instead of just plain SHA1
			$salt = substr(md5(time()),0,4);
			$encryptedPassword = '{SSHA}'.base64_encode(pack('H*',sha1($password.$salt)).$salt);

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
