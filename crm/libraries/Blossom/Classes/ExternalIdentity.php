<?php
/**
 * @copyright 2011-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Blossom\Classes;

interface ExternalIdentity
{
	/**
	 * Should load user data from storage
	 */
	public function __construct($username);

	/**
	 * Return whether the username, password combo is valid
	 *
	 * @param string $username
	 * @param string $password The unencrypted password
	 * @return bool
	 */
	public static function authenticate($username,$password);

	/**
	 * @return string
	 */
	public function getFirstname();
	public function getLastname();
	public function getEmail();
	public function getPhone();
	public function getAddress();
	public function getCity();
	public function getState();
	public function getZip();
}
